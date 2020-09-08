<?php

namespace App\Http\Requests\Subscriptions;

use App\Rules\SameGroupType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewSubscriptionRequest extends FormRequest
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
            'group_id' => ['required', 'exists:groups,id', new SameGroupType],
            'service_id' => [
                'required',
                Rule::exists('services', 'id')->where(function ($query) {
                    $query->where('is_active', 1);
                }),
                new SameGroupType,
            ],
            'payment_amount' => 'required|numeric',
            'pmt_rec_from' => 'required|string'
        ];
    }
}
