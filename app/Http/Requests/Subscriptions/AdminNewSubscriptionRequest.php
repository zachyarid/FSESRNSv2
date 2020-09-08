<?php

namespace App\Http\Requests\Subscriptions;

use App\Rules\SameGroupType;
use Illuminate\Foundation\Http\FormRequest;

class AdminNewSubscriptionRequest extends FormRequest
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
            'group_id' => ['required','exists:groups,id', new SameGroupType],
            'service_id' => ['required','exists:services,id', new SameGroupType]
        ];
    }
}
