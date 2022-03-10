<?php

namespace App\Http\Requests;

use App\DataTransferObjects\LoginDto;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use JetBrains\PhpStorm\ArrayShape;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    #[ArrayShape(['email' => "string[]", 'password' => "string"])]
    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => 'min:4',
        ];
    }
    
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data'    => $validator->errors(),
        ]));
    }
    
    public function getDto(): LoginDto
    {
        return new LoginDto();
    }
}
