<?php

use App\Http\Controllers\DiscountController;
use Illuminate\Support\Facades\Route;


Route::post('/discount/create',[DiscountController::class, 'store'])->name('discount.create');
Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts.index');
Route::get('/discount/{id}', [DiscountController::class, 'findDiscountById'])->name('discounts.findDiscountById');
Route::put('/discount/{id}', [DiscountController::class, 'update'])->name('discounts.update');
Route::delete('/discount/{id}', [DiscountController::class, 'destroy'])->name('discounts.destroy');

Route::get('/', function () {
    return view('welcome');
});
