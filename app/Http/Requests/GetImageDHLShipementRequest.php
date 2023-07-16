<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetImageDHLShipementRequest extends FormRequest
{
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
            'typeCode' => ['required', 'in:waybill,commercial-invoice,customs-entry', 'string'],
            'pickupYearAndMonth' => ['required', 'date_format:Y-m']
        ];
    }
}
