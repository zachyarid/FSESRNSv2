<?php

namespace App\Http\Requests\Payments;

use App\Rules\FullPayment;
use Illuminate\Foundation\Http\FormRequest;

class NewPaymentRequest extends FormRequest
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
            'subscription' => 'required|exists:subscriptions,id',
            'payment_group' => 'required',
            'payment_amount' => ['required', new FullPayment, 'numeric']
        ];
    }
}
