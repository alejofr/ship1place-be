<?php

namespace App\Http\Requests;

use App\Rules\PropertyReq;
use App\Traits\RulesCustomer;
use Illuminate\Foundation\Http\FormRequest;
//$this->isRequiredCustomer([...$this->rulesCustomer()])
class StoreCustomerRequest extends FormRequest
{
    use RulesCustomer;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'data' => ['required', 'array', new PropertyReq($this->isRequiredCustomer([...$this->rulesCustomer()]), 'data', false)]
        ];
    }
}
