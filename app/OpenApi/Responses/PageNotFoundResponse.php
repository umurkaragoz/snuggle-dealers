<?php

namespace App\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class PageNotFoundResponse extends ResponseFactory
{
    public function build(): Response
    {
        return Response::create('PageNotFoundError')
            ->description('Page Not Found')->statusCode(404);
    }
}
