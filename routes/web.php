<?php

use App\Livewire\Packages\PackageManagerComponent;
use App\Livewire\Vouchers\VoucherManager;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::redirect('/register', '/login');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/package-manager', PackageManagerComponent::class)->name('package-manager');
    Route::get('/voucher-manager', VoucherManager::class)->name('voucher-manager');
});
