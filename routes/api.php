<?php

use App\Http\Controllers\CouponController;
use App\Http\Controllers\DiscountController;
use Illuminate\Support\Facades\Route;


Route::post('/discount/create',[DiscountController::class, 'store'])->name('discount.create');
Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts.index');
Route::get('/discount/{id}', [DiscountController::class, 'findDiscountById'])->name('discounts.findDiscountById');
Route::put('/discount/{id}', [DiscountController::class, 'update'])->name('discounts.update');
Route::delete('/discount/{id}', [DiscountController::class, 'destroy'])->name('discounts.destroy');
Route::get('/discounts/total', [DiscountController::class, 'totalDiscounts'])->name('discounts.totalDiscounts');
Route::get('/discounts/id-and-name', [DiscountController::class, 'getIdAndName'])->name('discounts.idAndName');

Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
Route::post('/coupon/create',[CouponController::class, 'store'])->name('coupons.create');
Route::get('/coupon/id/{id}', [CouponController::class, 'findCouponById'])->name('coupons.findCouponById');
Route::get('/coupon/code/{code}', [CouponController::class, 'findCouponByCode'])->name('coupons.findCouponByCode');
Route::put('/coupon/{id}', [CouponController::class, 'update'])->name('coupons.update');


Route::get('/', function () {
    return view('welcome');
});
