<?php

namespace App\Http\Requests;

use App\Rules\IsObject;
use App\Rules\PropertyReq;
use Illuminate\Foundation\Http\FormRequest;

class GetRatesDHLRequest extends FormRequest
{
    public $customerDetails = [
        'postalCode' => ['required', 'string'],
        'cityName' => ['required', 'string'],
        'countryCode' => ['required', 'string'],
        'provinceCode' => ['string'],
        'addressLine1' => ['string'],
        'addressLine2' => ['string'],
        'addressLine3' => ['string'],
        'countyName' => ['string']
    ];

    public $packages = [
        'typeCode' => ['string', 'in:3BX,2BC,2BP,CE1,7BX,6BX,4BX,2BX,1CE,WB1,WB3,XPD,8BX,5BX,WB6,TBL,TBS,WB2'],
        'weight' => ['required', 'numeric'],
        'dimensions' => ['required']
    ];

    public $packagesDimensions = [
        'dimensions' => [
            'length' => ['required', 'numeric'],
            'width' => ['required', 'numeric'],
            'height' => ['required', 'numeric'],
        ]
    ];

    public $monetaryAmount = [
        'typeCode' => ['required', 'string', 'in:declaredValue,insuredValue'],
        'value' => ['required', 'numeric'],
        'currency' => ['required', 'string', 'min:3', 'max:3']
    ];

   

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
        $this->packages['dimensions'] = [...$this->packages['dimensions'], new IsObject];
        
        return [
            'shipperDetails' => ['required', new IsObject, new PropertyReq($this->customerDetails, 'shipperDetails')],
            'receiverDetails' => ['required', new IsObject, new PropertyReq($this->customerDetails, 'receiverDetails')],
            'plannedShippingDateAndTime' => ['required'],
            'unitOfMeasurement' => ['required', 'in:metric,imperial', 'string'],
            'isCustomsDeclarable' => ['required', 'boolean'],
            'packages' => ['required', 'array', new PropertyReq($this->packages, 'packages', false, $this->packagesDimensions)],
            'monetaryAmount' => ['required_if:isCustomsDeclarable,true', 'array',  new PropertyReq($this->monetaryAmount, 'monetaryAmount', false)]
        ];
        
    }
}
