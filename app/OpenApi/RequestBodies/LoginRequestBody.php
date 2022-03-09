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
        return RequestBody::create('Login')
            ->content(
                MediaType::formUrlEncoded()->schema(
                    Schema::object('User')
                        ->properties(
                            Schema::string('email')->description('Admin Email')->default('admin@buckhill.co.uk'),
                            Schema::string('password')->description('Admin Password')->default('admin'),
                        )
                        ->required('email', 'password'))
            )
            ->required();
    }
}
