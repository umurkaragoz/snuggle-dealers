<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
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

Route::post('/user/login',      [UserController::class, 'login']);
Route::post('/user/create',     [UserController::class, 'create']);

Route::middleware('auth')->group(function() {
    
    Route::middleware('admin-only')->prefix('admin')->group(function() {
        
        Route::get('/logout',   [AdminController::class, 'logout']);
        
        Route::get('/user-listing',             [AdminController::class, 'userListing']);
        Route::put('/user-edit/{uuid}',         [AdminController::class, 'userEdit']);
        Route::delete('/user-delete/{uuid}',    [AdminController::class, 'userDelete']);
    
    });
    
    Route::prefix('user')->group(function() {
        
        Route::get('/',   [UserController::class, 'show']);
        
        Route::get('/logout',       [UserController::class, 'logout']);
        Route::put('/edit',         [UserController::class, 'edit']);
        Route::delete('/delete',    [UserController::class, 'delete']);
    
    });
    
});
