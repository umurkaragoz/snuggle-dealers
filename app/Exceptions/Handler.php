<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];
    
    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];
    
    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function(Throwable $e) {
            // We are building an API, therefore we want all exceptions in JSON format.
            
            $httpStatusCode = 400;
            
            if ($this->isHttpException($e)) {
                // If we are processing a Http exception, we can try to extract a more useful status code than mere `400`.
                $httpStatusCode = $e->getStatusCode();
            }
            
            // Return a JSON response.
            return response()->json($e->getMessage(), $httpStatusCode);
        });
    }
}
