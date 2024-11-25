<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\AttendanceController;
use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\LeaveRequestController;
use App\Http\Controllers\Dashboard\permissionRequestController;
use App\Http\Controllers\Dashboard\EvaluationController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/',function () {
    return view('welcome');
});
// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/contact-us', [AuthController::class, 'contact_us'])->name('contact_us');
Route::post('/contact_us', [AuthController::class, 'post_contact_us'])->name('post_contact_us');
Route::get('/privacy_policy', [AuthController::class, 'privacy_policy'])->name('privacy_policy');
Route::get('/remove_account', [AuthController::class, 'remove_account'])->name('remove_account');
Route::get('/admin-dashboard/login', [AuthController::class, 'login_view'])->name('login.view');
Route::post('/admin-dashboard/login', [AuthController::class, 'login'])->name('login');
Route::get('/admin-dashboard', function () {
    
    if(!auth()->user()){
        return redirect('/admin-dashboard/login');
    }else{
        return redirect('/admin-dashboard/home');
    }
});
Route::group(['middleware' => ['admin'], 'prefix' => 'admin-dashboard'], function () {
    Route::get('/home', [AuthController::class, 'home'])->name('home');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/change_theme',[AuthController::class,'change_theme'])->name('change_theme');
        Route::any('/users', [UserController::class, 'index'])->name('users'); 
        Route::get('/users/create', [UserController::class, 'create'])->name('add.user');
        Route::post('/users/create', [UserController::class, 'store'])->name('create.user');
        Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('edit.user');
        Route::post('/user/update/{id}', [UserController::class, 'update'])->name('update.user');
        Route::get('/user/delete/{id}', [UserController::class, 'delete'])->name('delete.user');
        Route::post('/save-evaluation',[UserController::class, 'saveEvaluation'])->name('saveEvaluation');
        ///////////////////////////////////////////////////////////////////////////////
        Route::any('/admins', [AdminController::class, 'index'])->name('admins'); 
        Route::get('/admins/create', [AdminController::class, 'create'])->name('add.admin');
        Route::post('/admins/create', [AdminController::class, 'store'])->name('create.admin');
        Route::get('/admin/edit/{id}', [AdminController::class, 'edit'])->name('edit.admin');
        Route::post('/admin/update/{id}', [AdminController::class, 'update'])->name('update.admin');
        Route::get('/admin/delete/{id}', [AdminController::class, 'delete'])->name('delete.admin');
    /////////////////////////////////////////
        Route::any('/attendance', [AttendanceController::class, 'index'])->name('attendance'); 
        Route::get('/attendance/edit/{id}', [AttendanceController::class, 'edit'])->name('edit.attendance');
        Route::post('/attendance/update/{id}', [AttendanceController::class, 'update'])->name('update.attendance');
        Route::get('/attendance/delete/{id}', [AttendanceController::class, 'delete'])->name('delete.attendance');
        /////////////////////////////////////////
        Route::any('/leave_requests', [LeaveRequestController::class, 'index'])->name('leave_requests'); 
        Route::get('/leave_request/edit/{id}', [LeaveRequestController::class, 'edit'])->name('edit.leave_request');
        Route::post('/leave_request/update/{id}', [LeaveRequestController::class, 'update'])->name('update.leave_request');
        Route::get('/leave_request/delete/{id}', [LeaveRequestController::class, 'delete'])->name('delete.leave_request');
        /////////////////////////////////////////
        Route::any('/permission_requests', [permissionRequestController::class, 'index'])->name('permission_requests'); 
        Route::get('/permission_request/edit/{id}', [permissionRequestController::class, 'edit'])->name('edit.permission_request');
        Route::post('/permission_request/update/{id}', [permissionRequestController::class, 'update'])->name('update.permission_request');
        Route::get('/permission_request/delete/{id}', [permissionRequestController::class, 'delete'])->name('delete.permission_request');
         /////////////////////////////////////////
         Route::any('/evaluations', [EvaluationController::class, 'index'])->name('evaluations'); 
         Route::get('/evaluation/view/{id}', [EvaluationController::class, 'view'])->name('view.evaluation');
});