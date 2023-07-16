<?php

namespace App\Http\Controllers;

use App\Helpers\IsValidChange;
use App\Helpers\validJsonProperty;
use App\Http\Requests\StorePackageRequest;
use App\Http\Requests\UpdatePackageRequest;
use App\Services\PackageService;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/***
 * @group
 *
 * 
*/

class PackageController extends Controller
{
    use ApiResponse;
    private $packageService;
    private $userService;
    

    /**
     * Create a new controller instance.
     *
     * @return void
     * 
    */

    public function __construct(PackageService $packageService, UserService $userService)
    {
       $this->packageService = $packageService;
       $this->userService =  $userService;
    }

    /**
     * Return Packages LIST.
     *
     * @return Illuminate\Http\Response
    */

    public function index(Request $request)
    {
       return $this->successResponse($this->packageService->index($request->limit, $request->page, $request->search, $request->orderBy, $request->ascending, $request->user_id));
    }

    /**
     * Return Packages Search All.
     *
     * @return Illuminate\Http\Response
    */

    public function search(Request $request)
    {
       return $this->successResponse($this->packageService->search($request->search, $request->user_id));
    }


    /**
     * Create an instance of Package
     * 
     * @return  Illuminate\Http\Response
    */

    public function store(StorePackageRequest $request)
    {
        $this->userService->showUser($request->user_id);

        if( $this->packageService->isCheckName($request->name, $request->user_id) ){
            return $this->errorResponse('The name has already been taken.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if( isset($request->extra) && !validJsonProperty::isValid($request->extra) ){
            return $this->errorResponse('the extra property, it must be a json', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $request->all();

        if( array_key_exists('dimension', $data) && !isset($data['dimension']) ){
            return $this->errorResponse('Required dimension', Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        return $this->successResponse(['data' => $this->packageService->storePackage($data)]);
    }

     /**
      * Return an especify Package
      *
      * @return  Illuminate\Http\Response
    */

    public function show($id)
    {
        return $this->successResponse(['data' => $this->packageService->showPackage($id)]);
    }

    /**
      * Update the information of an existing Package
      *
      * @return  Illuminate\Http\Response
    */


    public function update($id, UpdatePackageRequest $request)
    {
        $package = $this->packageService->showPackage($id);

        if(  IsValidChange::compare($request->user_id, $package->user_id)  ){
            $this->userService->showUser($request->user_id);
        }

        if( 
            IsValidChange::compare($request->name, $package->name) && $this->packageService->isCheckName($request->name, $package->user_id)
            || IsValidChange::compare($request->name, $package->name) && IsValidChange::compare($request->user_id, $package->user_id && $this->packageService->isCheckName($request->name, $request->user_id))
        ){
            return $this->errorResponse('The name has already been taken.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $request->all();

        if( array_key_exists('dimension', $data) && !isset($data['dimension']) ){
            return $this->errorResponse('Required dimension', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if( isset($data['extra']) ){
            $data['extra'] = json_encode(json_decode($data['extra']));
        }

        $package->fill($data);

        if( $package->isClean() ){
            return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $package->update();

        return $this->successResponse(['data' => $package]);
    }

    /**
      * Removes an existing Package
      *
      * @return  Illuminate\Http\Response
    */

    public function destroy($id)
    {
        $package = $this->packageService->showPackage($id);

        $package->delete();

        return $this->successResponse(['data' => $package]);
    }
}
