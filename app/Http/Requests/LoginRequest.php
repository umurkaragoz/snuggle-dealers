<?php

namespace App\Http\Requests;

use App\DataTransferObjects\LoginDto;

class LoginRequest extends BaseRequest
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
            'email'    => ['required', 'email'],
            'password' => 'min:4',
        ];
    }
    
    public function getDto(): LoginDto
    {
        return new LoginDto();
    }
}
