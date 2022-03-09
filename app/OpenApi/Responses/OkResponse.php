<?php

namespace App\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class OkResponse extends ResponseFactory
{
    public function build(): Response
    {
        return Response::create('OkResponse')
            ->description('OK')->statusCode(200);
    }
}
