<?php

namespace App\Http\Requests;

use App\Rules\IsObject;
use App\Rules\PropertyReq;
use App\Rules\SubPropertyReq;
use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentDHLRequest extends FormRequest
{
    public $pickup = [
        'isRequested' => ['required', 'boolean'],
        'closeTime' => ['string'],
        'location' => ['string'],
        'specialInstructions' => ['array']
    ];

    public $packages = [
        'typeCode' => ['string', 'in:3BX,2BC,2BP,CE1,7BX,6BX,4BX,2BX,1CE,WB1,WB3,XPD,8BX,5BX,WB6,TBL,TBS,WB2'],
        'weight' => ['required', 'numeric'],
        'dimensions' => ['required'],
        'customerReferences' => ['array']
    ];

    public $packagesDimensions = [
        'dimensions' => [
            'length' => ['required', 'numeric'],
            'width' => ['required', 'numeric'],
            'height' => ['required', 'numeric'],
        ]
    ];

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
        'countyName' => ['string'],
        'country_id' => ['required', 'integer'],
        'city_id' => ['required', 'integer'],
        'province_id' => ['integer']
    ];

    public $contactInformation = [
        'email' => ['email'],
        'phone' => ['required', 'string'],
        'mobilePhone' => ['string'],
        'companyName' => ['required', 'string'],
        'fullName' => ['required', 'string']
    ];

    public $contentData = [
        'isCustomsDeclarable' => ['required', 'boolean'],
        'description' => ['required', 'string'],
        'incoterm' => ['required', 'string', 'in:EXW,FCA,CPT,CIP,DPU,DAP,DDP,FAS,FOB,CFR,CIF,DAF,DAT,DDU,DEQ,DES'],
        'unitOfMeasurement' => ['required', 'in:metric,imperial', 'string'],
        'exportDeclaration' => ['required_if:isCustomsDeclarable,true']
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
        $this->customerDetails['postalAddress'] = [...$this->customerDetails['postalAddress'], new IsObject];
        $this->customerDetails['contactInformation'] = [...$this->customerDetails['contactInformation'], new IsObject];
        $this->contentData['exportDeclaration'] = [... $this->contentData['exportDeclaration'], new IsObject];

        return [
            'plannedShippingDateAndTime' => ['required', 'date_format:Y-m-d'],
            'pickup' => ['required', new IsObject, new PropertyReq($this->pickup, 'pickup')],
            'shipperDetails' => ['required', new IsObject, new PropertyReq([...$this->customerDetails], 'shipperDetails'), new SubPropertyReq($this->postalAddress, 'postalAddress'), new SubPropertyReq($this->contactInformation, 'contactInformation')],
            'receiverDetails' => ['required', new IsObject, new PropertyReq([...$this->customerDetails], 'receiverDetails'),new SubPropertyReq($this->postalAddress, 'postalAddress'), new SubPropertyReq($this->contactInformation, 'contactInformation')],
            'productCode' => ['required', 'string', 'min:1', 'max:6'],
            'packages' => ['required', 'array', new PropertyReq($this->packages, 'packages', false, $this->packagesDimensions)],
            'content' => ['required', new IsObject, new PropertyReq($this->contentData, 'content')],
            'service' => ['required', new IsObject],
            'price' => ['required', 'numeric','regex:/^[\d]{0,11}(\.[\d]{2})?$/'],
            'currency' => ['required']
        ];
    }
}
