<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');
Route::get('/dashboard', DashboardController::class)->name('dashboard');

Route::get('/products/load-more', [ProductController::class, 'loadMore'])->name('products.load-more');
Route::get('/products/{product}/quick-view', [ProductController::class, 'quickView'])->name('products.quick-view');
Route::resource('products', ProductController::class);

Route::post('/demo/reset', [DemoController::class, 'reset'])->name('demo.reset');
