<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
// needs work!!!!
class UpdateProfileRequest extends FormRequest
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
            'fname' => 'required|string|max:191',
            'lname' => 'required|string|max:191',
            'username' => 'required|string|max:191|unique:users',
            'email' => 'required|string|email|max:191',
            'password' => 'confirmed',
            'personal_key' => 'required|string',
        ];
    }
}
