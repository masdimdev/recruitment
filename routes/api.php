<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => 'guest'], function () {
    Route::group([
        'as' => 'candidate.',
        'prefix' => '/candidate',
    ], function () {
        Route::group([
            'as' => 'auth.',
            'controller' => \App\Http\Controllers\Api\Candidate\AuthController::class,
            'prefix' => '/auth'
        ], function () {
            Route::post('/register', 'register')
                ->name('register');

            Route::post('/login', 'login')
                ->name('login');
        });
    });

    Route::group([
        'as' => 'company.',
        'prefix' => '/company',
    ], function () {
        Route::group([
            'as' => 'auth.',
            'controller' => \App\Http\Controllers\Api\Company\AuthController::class,
            'prefix' => '/auth'
        ], function () {
            Route::post('/register', 'register')
                ->name('register');

            Route::post('/login', 'login')
                ->name('login');
        });
    });
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group([
        'as' => 'candidate.',
        'prefix' => '/candidate',
    ], function () {
        Route::group([
            'as' => 'account.',
            'controller' => \App\Http\Controllers\Api\Candidate\AccountController::class,
            'prefix' => '/account'
        ], function () {
            Route::get('/', 'account')
                ->name('index');

            Route::post('/', 'updateAccount')
                ->name('update');

            Route::post('/logout', 'logout')
                ->name('logout');
        });
    });

    Route::group([
        'as' => 'company.',
        'prefix' => '/company',
    ], function () {
        Route::group([
            'as' => 'account.',
            'controller' => \App\Http\Controllers\Api\Company\AccountController::class,
            'prefix' => '/account'
        ], function () {
            Route::get('/', 'account')
                ->name('index');

            Route::post('/', 'updateAccount')
                ->name('update');

            Route::post('/logout', 'logout')
                ->name('logout');
        });
    });
});