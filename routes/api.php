<?php

use App\Http\Controllers\PesapalIpnServer;
use App\Livewire\Payments\PesapalIpn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/payments-clarifier', [PesapalIpnServer::class, 'index'])->name('voucher-payment-ipn');
Route::get('/payments', PesapalIpn::class)->name('voucher-payment');
