<?php

namespace App\OpenApi\RequestBodies;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\RequestBodyFactory;

class UserEditRequestBody extends RequestBodyFactory
{
    public function build(): RequestBody
    {
        $userObject = Schema::object('User')
            ->properties(
                Schema::string('email')->description('User email'),
                Schema::string('first_name')->description('User first name'),
                Schema::string('last_name')->description('User last name'),
                Schema::string('password')->description('User password'),
                Schema::string('password_confirmation')->description('User password'),
                Schema::string('address')->description('User main address'),
                Schema::string('avatar')->description('User avatar'),
                Schema::string('phone_number')->description('User main phone number'),
                Schema::string('is_marketing')->description('User marketing permission'),
            );
        
        return RequestBody::create('User Edit')
            ->content(
                MediaType::formUrlEncoded()->schema($userObject)
            )
            ->required();
    }
}
