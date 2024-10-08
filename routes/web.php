<?php

use App\Http\Controllers\RazorpayController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('payment',[RazorpayController::class,'index']);
Route::post('payment/create',[RazorpayController::class,'store'])->name('razorpay.payment.store');
Route::post('payment/failure',[RazorpayController::class,'failure'])->name('razorpay.payment.failure');
Route::get('/402', function () {
    return view('errors.402');
});
