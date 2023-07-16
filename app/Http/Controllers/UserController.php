<?php

namespace App\Http\Controllers;

use App\Helpers\IsValidChange;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\StoreUserSubClientRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Mail\RecoverPassword;
use App\Services\CityService;
use App\Services\CountryService;
use App\Services\Log\CreateLogRequest;
use App\Services\ProvinceService;
use App\Services\RoleService;
use App\Services\ShipmentService;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;


class UserController extends Controller
{
    use ApiResponse;
    private $userService;
    private $provinceService;
    private $countryService;
    private $cityService;
    private $serviceShipment;

    /**
     * Create a new controller instance.
     *
     * @return void
    */


    public function __construct(UserService $userService, ProvinceService $provinceService, CountryService $countryService, CityService $cityService, ShipmentService $serviceShipment)
    {
        $this->userService =  $userService;
        $this->provinceService = $provinceService;
        $this->countryService = $countryService;
        $this->cityService = $cityService;
        $this->serviceShipment = $serviceShipment;
    }

    /**
     * Return Users LIST.
     *
     * @return Illuminate\Http\Response
    */

    public function index(Request $request)
    {
        $idClient =  $request->user()->getRoleNames()->first() == 'Client' ? $request->user()->user_id : null;

        return $this->successResponse($this->userService->index($request->limit, $request->page, $request->search, $request->country_id, $request->province_id, $request->city_id, $idClient, $request->orderBy, $request->ascending));
    }

    public function belongs($user_id)
    {
        $idClient =  auth('sanctum')->user()->user_id;
        return $this->successResponse($this->userService->belongsToUser($user_id, $idClient));
    }

    /**
     * Return Users LIST.
     *
     * @return Illuminate\Http\Response
    */

    public function search(Request $request)
    {
        return $this->successResponse($this->userService->searchUser($request->search, $request->country_id, $request->province_id, $request->city_id, $request->userType, $request->limit, $request->page, $request->orderBy, $request->ascending));
    }

    /**
     * Return Users LIST.
     *
     * @return Illuminate\Http\Response
    */

    public function getAllClients()
    {
        return $this->successResponse(['data' => $this->userService->getAllClients()]);
    }

    /**
     * Create an instance of User sub client
     * 
     * @return  Illuminate\Http\Response
    */


    public function storeSubClient(StoreUserSubClientRequest $request)
    {
        $this->countryService->showCountry($request->country_id);
        $this->provinceService->showPronvince($request->province_id);
        $this->cityService->showCity($request->city_id);

        $user = $this->userService->storeUser('sub-client', $request->all());
        $user = RoleService::assing(3, 'api', $user);

        return $this->successResponse(['data' => $user]);
    }

    /**
      * Return an especify User
      *
      * @return  Illuminate\Http\Response
    */

    public function show($id)
    {
        $user = $this->userService->showUser($id);
        $user = json_decode(json_encode($user), true);

        return $this->successResponse(['data' => $this->userService->cleanObjectUser($user)]);
    }

    /**
      * Update the information of an existing User
      *
      * @return  Illuminate\Http\Response
    */

    public function update($id, UpdateUserRequest $request)
    {
        $user = $this->userService->showUser($id);

        if (IsValidChange::compare($request->country_id, $user->country_id)) {
            $this->countryService->showCountry($request->country_id);
        }

        if (IsValidChange::compare($request->province_id, $user->province_id)) {
            $this->provinceService->showPronvince($request->province_id);
        }

        if (IsValidChange::compare($request->city_id, $user->city_id)) {
            $this->cityService->showCity($request->city_id);
        }

        if ($user->getRoleNames()->first() == 'Admin' && IsValidChange::compare($request->user_id_parent, $user->user_id_parent)) {
            $this->userService->showUser($request->user_id_parent);
        }

        if (IsValidChange::compare($request->email, $user->email) && $this->userService->isEmail($request->email)) {
            return $this->errorResponse('The email has already been taken.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $log = new CreateLogRequest('update', $user);

        $user->fill($request->all());

        if ($request->change_password == 1) {
            $user->password = $this->userService->generate_bcrypt_password($request->password);
        }

        // if ($user->isClean()) {
        //     return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
        // }

        $user->update();
        $log->createLog($user);

        return $this->successResponse(['data' => $user]);
    }

    /**
      * Removes an existing User
      *
      * @return  Illuminate\Http\Response
    */

    public function destroy($id)
    {
        $user = $this->userService->showUser($id);
        $log = new CreateLogRequest('delete', $user);

        $isShipment = $this->serviceShipment->checkShipmentByUser($user->user_id);
        $msg = 'Usuario eliminado';

        if ($user->getRoleNames()->first() == 'Client') {
            if( $isShipment ){
                $this->userService->disableSubClients($user->user_id);
            }else{
                $this->userService->deleteSubClients($user->user_id);
            }
        }

        if( $isShipment ){
            $user->status = false;
            $user->update();
            $msg = 'No se elimino el usuario, solo se desactivo. Debido a los shipment existente...';
        }else{
            $user->delete();
        }

        $log->createLog();

        return $this->successResponse(['data' => $user, 'message' => $msg]);
    }

    /**
      * Update an existing user information and deactivate
      *
      * @return  Illuminate\Http\Response
    */

    public function disableUser($id)
    {
        $user = $this->userService->showUser($id);
        $log = new CreateLogRequest('delete', $user);

        if ($user->getRoleNames()->first() == 'Client') {
            $this->userService->disableSubClients($user->user_id);
        }

        $user->status = false;

        $user->update();
        $log->createLog($user);

        return $this->successResponse(['data' => $user]);
    }

    /**
      * Recover password and send to email
      *
      * @return  Illuminate\Http\Response
    */

    public function recoverPassword(EmailRequest $request)
    {
        $user = $this->userService->getUserForEmail($request->email);

        if ($user == null) {
            return $this->errorResponse('Sorry the email is not registered', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $log = new CreateLogRequest('delete', $user);

        $newPassword = $this->userService->generatePassword();

        Mail::to($user->email)
            ->send(new RecoverPassword($newPassword));

        $user->password = $this->userService->generate_bcrypt_password($newPassword);
        $user->change_password = true;

        $user->update();
        $log->createLog($user);

        return $this->successResponse('The new password has been sent to the email');
    }

     /**
      * update user password
      *
      * @return  Illuminate\Http\Response
    */

    public function changePassword(PasswordRequest $request)
    {
        $user = $this->userService->showUser($request->user()->user_id);
        $user->password = $this->userService->generate_bcrypt_password($request->password);

        $user->update();

        return $this->successResponse(['data' => $user]);
    }
}
