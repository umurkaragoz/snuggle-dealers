<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// @formatter:off
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::pattern('uuid',  '[a-zA-Z0-9\-]+');

Route::post('/admin/login',     [AdminController::class, 'login']);
Route::post('/admin/create',    [AdminController::class, 'create']);

Route::middleware('auth')->group(function() {
    
    Route::prefix('admin')->group(function() {
        
        Route::get('/logout',   [AdminController::class, 'logout']);
        
        Route::get('/user-listing',         [AdminController::class, 'userListing']);
        // Route::get('/user-edit/{uuid}',     [AdminController::class, 'userEdit']);
        Route::delete('/user-delete/{uuid}',   [AdminController::class, 'userDelete']);
    
    });
    
});
