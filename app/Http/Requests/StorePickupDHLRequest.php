<?php

namespace App\Http\Requests;

use App\Rules\IsObject;
use App\Rules\PropertyReq;
use App\Rules\SubPropertyReq;
use Illuminate\Foundation\Http\FormRequest;

class StorePickupDHLRequest extends FormRequest
{

    public $customerDetails = [
        'postalAddress' => ['required'],
        'contactInformation' => ['required']
    ];

    public $postalAddress = [
        'postalCode' => ['required', 'string'],
        'cityName' => ['required', 'string'],
        'countryCode' => ['required', 'string'],
        'provinceCode' => ['string'],
        'addressLine1' => ['required', 'string'],
        'addressLine2' => ['string'],
        'addressLine3' => ['string'],
        'countyName' => ['string']
    ];

    public $contactInformation = [
        'email' => ['email'],
        'phone' => ['required', 'string'],
        'mobilePhone' => ['string'],
        'companyName' => ['required', 'string'],
        'fullName' => ['required', 'string']
    ];

    public $shipmentDetailsData = [
        'isCustomsDeclarable' => ['required', 'boolean'],
        'unitOfMeasurement' => ['required', 'in:metric,imperial', 'string']
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
        $this->customerDetails['postalAddress'] = [...$this->customerDetails['postalAddress'], new IsObject];
        $this->customerDetails['contactInformation'] = [...$this->customerDetails['contactInformation'], new IsObject];

        return [
            'shipment_id' => ['integer', 'required'],
            'shipperDetails' => ['required', new IsObject, new PropertyReq($this->customerDetails, 'shipperDetails'), new SubPropertyReq($this->postalAddress, 'postalAddress'), new SubPropertyReq($this->contactInformation, 'contactInformation')],
            'receiverDetails' => ['required', new IsObject, new PropertyReq($this->customerDetails, 'receiverDetails'),new SubPropertyReq($this->postalAddress, 'postalAddress'), new SubPropertyReq($this->contactInformation, 'contactInformation')],
            'shipmentDetails' => ['required', new IsObject, new PropertyReq($this->shipmentDetailsData, 'shipmentDetails')],
            'plannedPickupDate' => ['required', 'date_format:Y-m-d'],
            'plannedPickupTime' => ['required', 'date_format:H:i:s']
        ];
    }
}
