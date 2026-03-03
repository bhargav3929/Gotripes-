<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name'         => ['required'],
            'email'        => ['required', 'unique:users,email,' . request()->route('user')->id],
            'password'     => ['nullable', 'min:6'],
            'access_type'  => ['required', 'in:full,specific'],
            'roles'        => ['nullable', 'array'],
            'roles.*'      => ['integer', 'exists:roles,id'],
            'modules'      => ['nullable', 'array'],
            'modules.*'    => ['string', 'in:uaeactivities,announcements,homepageads'],
        ];
    }
}
