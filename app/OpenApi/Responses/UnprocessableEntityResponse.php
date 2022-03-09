<?php

namespace App\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class UnprocessableEntityResponse extends ResponseFactory
{
    public function build(): Response
    {
        return Response::create('UnprocessableEntityResponse')
            ->description('Unprocessable Entity')->statusCode(422);
    }
}
