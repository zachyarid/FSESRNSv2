<?php

namespace App\Http\Requests\UserAccess;

use App\Rules\UserAccessExists;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewAdditionalUserAccessRequest extends FormRequest
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
            //'subscription' => 'required|exists:subscriptions,id',
            'subscription' => [
                'required',
                Rule::exists('subscriptions', 'id')->where(function ($query) {
                    $query->where('status', 1);
                }),
            ],
            'username' => ['required' ,'exists:users', new UserAccessExists]
        ];
    }
}
