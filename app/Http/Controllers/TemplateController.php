<?php

namespace App\Http\Controllers;

use App\Helpers\IsValidChange;
use App\Http\Requests\StoreTemplateRequest;
use App\Http\Requests\UpdateTemplateRequest;
use App\Services\CustomerService;
use App\Services\TemplateService;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TemplateController extends Controller
{
    use ApiResponse;
    private $userService;
    private $customerService;
    private $templateService;

    /**
     * Create a new controller instance.
     *
     * @return void
    */

    public function __construct(UserService $userService, CustomerService $customerService, TemplateService $templateService)
    {
        $this->userService =  $userService;
        $this->customerService = $customerService;
        $this->templateService = $templateService;
    }

    /**
     * Return Templates LIST.
     *
     * @return Illuminate\Http\Response
    */


    public function index(Request $request)
    {
        $this->userService->showUser($request->user_id);

        return $this->successResponse(['data' => $this->templateService->index($request->limit, $request->page, $request->orderBy, $request->ascending, $request->user_id)]);
    }

    /**
     * Create an instance of Template
     * 
     * @return  Illuminate\Http\Response
    */

    public function store(StoreTemplateRequest $request)
    {
        $this->userService->showUser($request->user_id); // check user
        $this->customerService->showCustomer($request->sender_id); //check customer sender
        $this->customerService->showCustomer($request->receiver_id); //check customer receiver

        return $this->successResponse(['data'=> $this->templateService->storeTemplate($request->all())]);
    }

    /**
      * Return an especify Template
      *
      * @return  Illuminate\Http\Response
    */


    public function show($id)
    {
        $template = $this->templateService->showTemplate($id);

        $sender = $this->customerService->showCustomer($template->sender_id);
        $receiver =  $this->customerService->showCustomer($template->receiver_id);

        $sender->extra = json_decode($sender->extra);
        $receiver->extra = json_decode($receiver->extra);
        $template->pieces = json_decode($template->pieces);

        unset($template->sender_id);
        unset($template->receiver_id);

        $template->sender = $sender;
        $template->receiver = $receiver;

        return $this->successResponse(['data'=> $template]);
    }

    /**
      * Update the information of an existing Template
      *
      * @return  Illuminate\Http\Response
    */

    public function update($id, UpdateTemplateRequest $request)
    {
        $template = $this->templateService->showTemplate($id);

        if(  IsValidChange::compare($request->user_id, $template->user_id)  ){
            $this->userService->showUser($request->user_id);
        }

        if(  IsValidChange::compare($request->sender_id, $template->sender_id)  ){
            $this->customerService->showCustomer($request->sender_id);
        }

        if(  IsValidChange::compare($request->receiver_id, $template->receiver_id)  ){
            $this->customerService->showCustomer($request->receiver_id);
        }

        $data = $request->all();

        if( isset($data['pieces']) ){
            $data['pieces'] = json_encode($data['pieces']);
        }

        $template->fill($data);

        if( $template->isClean() ){
            return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $template->update();

        return $this->successResponse(['data' => $template]);
    }

    /**
      * Removes an existing Template
      *
      * @return  Illuminate\Http\Response
    */

    public function destroy($id)
    {
        $template = $this->templateService->showTemplate($id);
        $template->delete();

        return $this->successResponse(['data' => $template]);

    }
}
