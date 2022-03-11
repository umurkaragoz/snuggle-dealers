<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
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
        // We are building an API, therefore we want all exceptions rendered in JSON format.
        $this->renderable(function (Throwable $e) {
            $httpStatusCode = 400;
            
            // If we are processing an Http exception, we can try to extract a more useful status code than mere `400`.
            if ($e instanceof UnauthorizedException) {
                $httpStatusCode = $e->getCode();
            }
            
            if (config('app.env') === 'production') {
                // Return a JSON response. Show only a simple message on production environment.
                return response()->json([
                    'message' => $e->getMessage(),
                ], $httpStatusCode);
            } else {
                // Print error details outside of the production environment.
                return response()->json([
                    'message' => $e->getMessage(),
                    'data'    => [
                        'type'  => get_class($e),
                        'trace' => $e->getTrace(),
                    ],
                ], $httpStatusCode);
            }
        });
    }
}
