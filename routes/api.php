<?php

use App\Http\Controllers\CouponController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;


Route::post('/discount/create',[DiscountController::class, 'store'])->name('discount.create');
Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts.index');
Route::get('/discount/{id}', [DiscountController::class, 'findDiscountById'])->name('discounts.findDiscountById');
Route::post('/discounts/find-by-ids', [DiscountController::class, 'findDiscountsByIds'])->name('discounts.findDiscountsByIds');
Route::get('/discounts/all', [DiscountController::class, 'getAllDiscounts'])->name('discounts.index');


Route::put('/discount/{id}', [DiscountController::class, 'update'])->name('discounts.update');
Route::delete('/discount/{id}', [DiscountController::class, 'destroy'])->name('discounts.destroy');
Route::get('/discounts/total', [DiscountController::class, 'totalDiscounts'])->name('discounts.totalDiscounts');
Route::get('/discounts/id-and-name', [DiscountController::class, 'getIdAndName'])->name('discounts.idAndName');
Route::get('/discounts/with-coupons', [DiscountController::class, 'getDiscountsWithCoupons'])->name('discounts.withCoupons');

Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
Route::post('/coupon/create',[CouponController::class, 'store'])->name('coupons.create');
Route::get('/coupon/id/{id}', [CouponController::class, 'findCouponById'])->name('coupons.findCouponById');
Route::get('/coupon/code/{code}', [CouponController::class, 'findCouponByCode'])->name('coupons.findCouponByCode');
Route::put('/coupon/{id}', [CouponController::class, 'update'])->name('coupons.update');
Route::put('/coupon/{id}/status', [CouponController::class, 'updateStatus'])->name('coupons.updateStatus');
Route::put('/coupon/{id}/times-used', [CouponController::class, 'decrementTimesUsed'])->name('coupons.decrementTimesUsed');
Route::delete('/coupon/{id}', [CouponController::class, 'destroy'])->name('coupons.destroy');
Route::get('/coupons/discount/{id}', [CouponController::class, 'findCouponByDiscountIdAndCode'])->name('coupons.findCouponByDiscountIdAndCode');

Route::get('/reports',[ReportController::class, 'index'])->name('reports.index');
Route::get('/', function () {
    return view('welcome');
});
