<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexShipementRequest extends FormRequest
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
            'user_id'  => ['integer', 'required'],
            'page' => ['integer'],
            'limit' => ['integer'],
            'ascending' => ['boolean'],
            'orderBy' => ['string', 'in:price,tracking_number,s_date'],
            'service' => ['string', 'in:dhl'],
            'dateFrom' => ['date_format:Y-m-d'],
            'dateTo' => ['date_format:Y-m-d'],
            'status' => ['string', 'in:outstanding,paid,canceled'],
            'waybill' => ['string']
        ];
    }
}
