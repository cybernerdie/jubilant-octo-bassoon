<?php

use App\Http\Controllers\API\Auth\AuthenticationController;
use App\Http\Controllers\API\UserManagement\UserProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthenticationController::class, 'register'])->name('register');
Route::post('login', [AuthenticationController::class, 'login'])->name('login');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [AuthenticationController::class, 'logout'])->name('logout');
    Route::get('account/profile', [AuthenticationController::class, 'getUserProfile'])->name('user.profile');

    Route::post('account/upload_cv', [UserProfileController::class, 'uploadCv'])->name('user.cv.upload');
});
