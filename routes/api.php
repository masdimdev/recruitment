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
        'middleware' => 'ability:as-candidate',
        'prefix' => '/candidate',
    ], function () {
        Route::group([
            'as' => 'account.',
            'controller' => \App\Http\Controllers\Api\Candidate\AccountController::class,
            'prefix' => '/account'
        ], function () {
            Route::get('/', 'account')
                ->name('index');

            Route::match(['POST', 'PATCH'], '/', 'update')
                ->name('update');

            Route::post('/logout', 'logout')
                ->name('logout');
        });

        Route::group([
            'as' => 'profile.',
            'controller' => \App\Http\Controllers\Api\Candidate\ProfileController::class,
            'prefix' => '/profile'
        ], function () {
            Route::get('/', 'profile')
                ->name('index');

            Route::match(['POST', 'PATCH'], '/', 'update')
                ->name('update');
        });
    });

    Route::group([
        'as' => 'company.',
        'middleware' => 'ability:as-company',
        'prefix' => '/company',
    ], function () {
        Route::group([
            'as' => 'account.',
            'controller' => \App\Http\Controllers\Api\Company\AccountController::class,
            'prefix' => '/account'
        ], function () {
            Route::get('/', 'account')
                ->name('index');

            Route::match(['POST', 'PATCH'], '/', 'update')
                ->name('update');

            Route::post('/logout', 'logout')
                ->name('logout');
        });

        Route::group([
            'as' => 'profile.',
            'controller' => \App\Http\Controllers\Api\Company\ProfileController::class,
            'prefix' => '/profile'
        ], function () {
            Route::get('/', 'profile')
                ->name('index');

            Route::match(['POST', 'PATCH'], '/', 'update')
                ->name('update');
        });
    });
});

Route::group([
    'as' => 'public.',
    'prefix' => '/public'
], function () {
    Route::get('/candidate/{candidateId}', [\App\Http\Controllers\Api\General\CandidateController::class, 'profile'])
        ->name('candidate.profile');

    Route::get('/company/{companyId}', [\App\Http\Controllers\Api\General\CompanyController::class, 'profile'])
        ->name('company.profile');

    Route::get('/job-category', [\App\Http\Controllers\Api\General\JobCategoryController::class, 'index'])
        ->name('job-category.index');
});