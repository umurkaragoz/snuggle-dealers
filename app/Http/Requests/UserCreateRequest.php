<?php

namespace App\Http\Requests;

use App\DataTransferObjects\UserCreateDto;

class UserCreateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function rules(): array
    {
        return [
            'email'        => ['required', 'email', 'unique:users'],
            'first_name'   => ['required', 'between:3,35'],
            'last_name'    => ['required', 'between:3,35'],
            'password'     => ['required', 'confirmed', 'min:3'],
            'address'      => ['required', 'between:3,255'],
            'avatar'       => ['required', 'size:36'],
            'phone_number' => ['required', 'between:3,35'],
            'is_marketing' => ['boolean'],
        ];
    }
    
    public function getDto(): UserCreateDto
    {
        return new UserCreateDto();
    }
}
