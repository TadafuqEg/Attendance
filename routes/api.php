<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\RequestController;
use App\Http\Controllers\API\ReportController;
/*
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
Route::get('/create_attendance',[AttendanceController::class,'create_attendance'])->name('create_attendance');
Route::get('/notify_user',[AttendanceController::class,'notify_user'])->name('notify_user');

//Route::post('/register',[AuthController::class,'register'])->name('register');
Route::post('/login',[AuthController::class,'login'])->name('login');

Route::middleware('auth:sanctum')->group( function () {
    Route::post('/logout',[AuthController::class,'logout'])->name('logout');
    Route::post('/device_tocken',[AuthController::class,'device_tocken'])->name('device_tocken');
    Route::get('/user_notification',[AuthController::class,'user_notification'])->name('user_notification');
    Route::post('/seen_notification',[AuthController::class,'seen_notification'])->name('seen_notification');
    Route::post('/update_password',[AuthController::class,'update_password'])->name('update_password');
    Route::get('/profile',[AuthController::class,'profile'])->name('profile');
    Route::get('/home',[AttendanceController::class,'home'])->name('home');
    Route::post('/check_in_out',[AttendanceController::class,'check_in_out'])->name('check_in_out');
    Route::post('/update_profile',[AuthController::class,'update_profile'])->name('update_profile');

    Route::get('/attendance',[AttendanceController::class,'attendance'])->name('attendance');
    Route::get('/all_auth_user_attendance',[AttendanceController::class,'all_user_attendance'])->name('all_user_attendance');
    //////////////////////////////////////////////////////////////////
    
    Route::get('/emergency_vacation',[RequestController::class,'emergency_vacation'])->name('emergency_vacation');
    Route::get('/ordinary_vacation',[RequestController::class,'ordinary_vacation'])->name('ordinary_vacation');
    Route::post('/sick_vacation_request',[RequestController::class,'sick_vacation_request'])->name('sick_vacation_request');
    
    Route::post('/emergency_vacation_request',[RequestController::class,'emergency_vacation_request'])->name('emergency_vacation_request');
    Route::post('/ordinary_vacation_request',[RequestController::class,'ordinary_vacation_request'])->name('ordinary_vacation_request');
    Route::post('/leave_permission_request',[RequestController::class,'leave_permission_request'])->name('leave_permission_request');
    Route::get('/all_requests',[RequestController::class,'all_requests'])->name('all_requests');
    Route::get('/all_permissions',[RequestController::class,'all_permissions'])->name('all_permissions');
    Route::get('/get_one_request',[RequestController::class,'get_one_request'])->name('get_one_request');
    Route::get('/get_one_permission',[RequestController::class,'get_one_permission'])->name('get_one_permission');

    Route::get('/report',[ReportController::class,'index'])->name('report');

});


