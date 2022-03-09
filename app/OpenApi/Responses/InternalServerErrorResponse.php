<?php

namespace App\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class InternalServerErrorResponse extends ResponseFactory
{
    public function build(): Response
    {
        return Response::create('InternalServerError')
            ->description('Internal Server Error')->statusCode(500);
    }
}
