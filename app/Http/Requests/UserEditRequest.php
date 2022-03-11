<?php

namespace App\Http\Requests;

use App\DataTransferObjects\UserCreateDto;

class UserEditRequest extends BaseRequest
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
            'email'        => ['email', 'unique:users'],
            'first_name'   => ['between:3,35'],
            'last_name'    => ['between:3,35'],
            'password'     => ['confirmed', 'min:3'],
            'address'      => ['between:3,255'],
            'avatar'       => ['size:36'],
            'phone_number' => ['between:3,35'],
            'is_marketing' => ['boolean'],
        ];
    }
    
    public function getDto(): UserCreateDto
    {
        return new UserCreateDto();
    }
}
