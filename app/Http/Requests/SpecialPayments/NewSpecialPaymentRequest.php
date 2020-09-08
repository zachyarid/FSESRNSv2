<?php

namespace App\Http\Requests\SpecialPayments;

use App\Rules\IsYourGroup;
use Illuminate\Foundation\Http\FormRequest;

class NewSpecialPaymentRequest extends FormRequest
{
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
     * @return array
     */
    public function rules()
    {
        return [
            'group_id' => ['required', 'exists:groups,id', new IsYourGroup],
            'payment_amount' => 'required|numeric',
            'payment_type' => 'required',
            'frequency' => 'required|in:1,7,14,30,90,180,360'
        ];
    }
}
