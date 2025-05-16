<?php

use App\Http\Controllers\CouponController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/discounts/with-coupons', [DiscountController::class, 'getDiscountsWithCoupons'])->name('discounts.withCoupons');
Route::post('/discounts',[DiscountController::class, 'store'])->name('discount.create');
Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts.index');
Route::get('/discounts/{id}', [DiscountController::class, 'findDiscountById'])->name('discounts.findDiscountById');
Route::post('/discounts/find-by-ids', [DiscountController::class, 'findDiscountsByIds'])->name('discounts.findDiscountsByIds');

//Route::get('/discounts/all', [DiscountController::class, 'getAllDiscounts'])->name('discounts.index');
Route::get('/discounts/id-and-name', [DiscountController::class, 'getIdAndName'])->name('discounts.idAndName');
Route::post('/discounts/affiliate-partners',[DiscountController::class, 'UpdateOrCreateDiscountInAffiliatePartner'])->name('discounts.UpdateOrCreateDiscountInAffiliatePartner');

Route::put('/discounts/{id}', [DiscountController::class, 'update'])->name('discounts.update');
Route::delete('/discounts/{id}', [DiscountController::class, 'destroy'])->name('discounts.destroy');

Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
Route::post('/coupons',[CouponController::class, 'store'])->name('coupons.create');
Route::get('/coupons/id/{id}', [CouponController::class, 'findCouponById'])->name('coupons.findCouponById');
Route::get('/coupons/code/{code}', [CouponController::class, 'findCouponByCode'])->name('coupons.findCouponByCode');
Route::put('/coupons/{id}', [CouponController::class, 'update'])->name('coupons.update');
Route::put('/coupons/{id}/status', [CouponController::class, 'updateStatus'])->name('coupons.updateStatus');
Route::put('/coupons/{id}/times-used', [CouponController::class, 'decrementTimesUsed'])->name('coupons.decrementTimesUsed');
Route::delete('/coupons/{id}', [CouponController::class, 'destroy'])->name('coupons.destroy');
Route::get('/coupons/discount/{id}', [CouponController::class, 'findCouponByDiscountIdAndCode'])->name('coupons.findCouponByDiscountIdAndCode');
Route::get('/coupons/discount/{id}/shop/{shop}/', [CouponController::class, 'findCouponByDiscountIdAndShop'])->name('coupons.findCouponByDiscountIdAndShop');
Route::get('/reports',[ReportController::class, 'index'])->name('reports.index');
