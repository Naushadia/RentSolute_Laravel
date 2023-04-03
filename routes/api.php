<?php

use App\Http\Controllers\amenityController;
use App\Http\Controllers\authController;
use App\Http\Controllers\propertyController;
use App\Http\Controllers\questionController;
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

Route::controller(authController::class)->group(function($router){
    Route::post('/forget-password', 'forgotPassword');
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/password/reset/{token}', 'resetPassword')->name('password.reset');
    Route::get('/getuser', 'getUser');
    Route::post('/updatepassword', 'updatePassword');
    Route::post('/dashboard', 'dashboard');
});

Route::get('/profile', function (Request $request) {
    // Access authenticated user data
    // $user = $request->user;
    return response($request->user);
})->middleware('requireUser');

Route::controller(amenityController::class)->group(function(){
    Route::post('/amenity', 'postAmenity');
    Route::get('/getamenity', 'getAmenity');
    Route::get('/allgetamenity', 'allGetAmenity');
    Route::delete('/destroyamenity', 'destroyAmenity');
});

Route::controller(questionController::class)->group(function(){
    Route::post('/postquestion', 'addQuestion');
    Route::get('/getquestion', 'getQuestions');
    Route::delete('/destroyquestion', 'destroyQuestion');
    Route::get('/getallquestion', 'allGetQuestion');
});

Route::controller(propertyController::class)->group(function(){
    Route::post('/postproperty', 'postProperty');
    Route::get('/allgetproperty', 'allGetProperty');
    Route::get('/getproperty', 'getProperty');
    Route::delete('/destroy/{id}', 'destroy');
    Route::post('/image','image');
    Route::delete('/destroyimage/{id}', 'destroyImage');
    Route::get('/allgetimage', 'allGetImage');
    Route::get('/allimage', 'getImage');
    Route::patch('/putproperty', 'putProperty');
});

