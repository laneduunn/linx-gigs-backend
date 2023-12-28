<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Jobs\JobController;
use App\Http\Controllers\Api\Jobs\CompanyController;
use App\Http\Controllers\Api\Jobs\ApplicationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('/signup', 'register');
    Route::post('/login', 'login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', 'logout');
    });
});

/**
 * Job routes
 */
Route::controller(JobController::class)->group(function () {
    Route::get('/jobs', 'getJobs');
    Route::get('/job/{id}', 'getJobsOfSpecificEmployer');
    Route::post('/job', 'searchJob');
    Route::delete('/delete-job/{id}', 'deleteJob');
});

Route::post('/create-job', [JobController::class, 'createJob']);
/**
 * Company routes
 */
Route::controller(CompanyController::class)->group(function () {
    Route::post('/company', 'createCompany');
    Route::get('/company/{id}', 'getCompany');
    Route::put('/company/{id}', 'editCompany');
});

/**
 * Application routes
 */

Route::controller(ApplicationController::class)->group(function () {
    Route::post('/application', 'createApplication');
    Route::get('/application/{id}', 'getAllApplication');
    Route::put('/application/{id}', 'acceptApplication');
    Route::delete('/application/{id}', 'cancelApplication');
});
