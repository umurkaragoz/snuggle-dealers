<?php

namespace App\OpenApi\RequestBodies;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\RequestBodyFactory;

class LoginRequestBody extends RequestBodyFactory
{
    public function build(): RequestBody
    {
        $userObject = Schema::object('User')
            ->properties(
                Schema::string('email')->description('Admin email')->default('admin@buckhill.co.uk'),
                Schema::string('password')->description('Admin password')->default('admin'),
            )
            ->required('email', 'password');
        
        return RequestBody::create('Login')
            ->content(
                MediaType::formUrlEncoded()->schema($userObject)
            )
            ->required();
    }
}
