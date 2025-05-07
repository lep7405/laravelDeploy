<?php

use App\Http\Controllers\DiscountController;
use Illuminate\Support\Facades\Route;


Route::post('/discount/create',[DiscountController::class, 'store'])->name('discount.create');
Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts.index');
Route::get('/', function () {
    return view('welcome');
});
