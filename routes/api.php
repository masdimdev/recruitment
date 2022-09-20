<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;

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
            'controller' => Api\Candidate\AuthController::class,
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
            'controller' => Api\Company\AuthController::class,
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
            'controller' => Api\Candidate\AccountController::class,
            'prefix' => '/account'
        ], function () {
            Route::get('/', 'account')
                ->name('index');

            Route::patch('/', 'update')
                ->name('update');

            Route::post('/logout', 'logout')
                ->name('logout');
        });

        Route::group([
            'as' => 'profile.',
            'controller' => Api\Candidate\ProfileController::class,
            'prefix' => '/profile'
        ], function () {
            Route::get('/', 'profile')
                ->name('index');

            Route::patch('/', 'update')
                ->name('update');
        });

        Route::group([
            'as' => 'job-application.',
            'controller' => Api\Candidate\JobApplicationController::class,
            'prefix' => '/job-application'
        ], function () {
            Route::get('/', 'index')
                ->name('index');

            Route::post('/', 'store')
                ->name('store');

            Route::get('/{applicationId}', 'show')
                ->name('show');
        });

        Route::group([
            'as' => 'notification.',
            'controller' => Api\Candidate\NotificationController::class,
            'prefix' => '/notification'
        ], function () {
            Route::get('/', 'index')
                ->name('index');
        });
    });

    Route::group([
        'as' => 'company.',
        'middleware' => 'ability:as-company',
        'prefix' => '/company',
    ], function () {
        Route::group([
            'as' => 'account.',
            'controller' => Api\Company\AccountController::class,
            'prefix' => '/account'
        ], function () {
            Route::get('/', 'account')
                ->name('index');

            Route::patch('/', 'update')
                ->name('update');

            Route::post('/logout', 'logout')
                ->name('logout');
        });

        Route::group([
            'as' => 'profile.',
            'controller' => Api\Company\ProfileController::class,
            'prefix' => '/profile'
        ], function () {
            Route::get('/', 'profile')
                ->name('index');

            Route::patch('/', 'update')
                ->name('update');
        });

        Route::group([
            'as' => 'job-vacancy.',
            'controller' => Api\Company\JobVacancyController::class,
            'prefix' => '/job-vacancy'
        ], function () {
            Route::get('/', 'index')
                ->name('index');

            Route::get('/{jobId}', 'show')
                ->name('show');

            Route::post('/', 'store')
                ->name('store');

            Route::patch('/{jobId}', 'update')
                ->name('update');

            Route::delete('/{jobId}', 'destroy')
                ->name('delete');

            Route::get('/{jobId}/application', 'jobApplication')
                ->name('job-application');
        });

        Route::group([
            'as' => 'job-application.',
            'controller' => Api\Company\JobApplicationController::class,
            'prefix' => '/job-application'
        ], function () {
            Route::get('/', 'index')
                ->name('index');

            Route::get('/{applicationId}', 'show')
                ->name('show');

            Route::patch('/{applicationId}', 'update')
                ->name('update');
        });
    });
});

Route::group([
    'as' => 'public.',
    'prefix' => '/public'
], function () {
    Route::get('/candidate/{candidateId}', [Api\General\CandidateController::class, 'show'])
        ->name('candidate.show');

    Route::get('/company/{companyId}', [Api\General\CompanyController::class, 'show'])
        ->name('company.show');

    Route::get('/company/{companyId}/job-vacancy', [Api\General\CompanyController::class, 'jobVacancy'])
        ->name('company.job-vacancy');

    Route::get('/job-category', [Api\General\JobCategoryController::class, 'index'])
        ->name('job-category.index');

    Route::get('/job-vacancy', [Api\General\JobVacancyController::class, 'index'])
        ->name('job-vacancy.index');

    Route::get('/job-vacancy/{jobId}', [Api\General\JobVacancyController::class, 'show'])
        ->name('job-vacancy.show');
});