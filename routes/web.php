<?php

use App\Livewire\Clients\WelcomePage;
use App\Livewire\Packages\PackageManagerComponent;
use App\Livewire\Payments\PaymentSettingsPage;
use App\Livewire\Vouchers\VoucherManager;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomePage::class)->name('welcome');

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
    Route::get('/payment-settings', PaymentSettingsPage::class)->name('payment-settings');

});
