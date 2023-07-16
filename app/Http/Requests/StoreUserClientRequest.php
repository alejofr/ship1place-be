<?php

namespace App\Http\Requests;

use App\Traits\RulesStoreUser;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserClientRequest extends FormRequest
{
    use RulesStoreUser;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [...$this->isRequiredUser([...$this->rulesUser()]), 'byCreated' => ['boolean', 'required']];
    }
}
