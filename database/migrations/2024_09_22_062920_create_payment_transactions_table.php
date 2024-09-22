<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('name of client');
            $table->string('email')->comment('email of client');
            $table->string('phone')->comment('phone of client');
            $table->foreignId('package_id')->constrained();
            $table->string('status')->comment('status of transaction')->default('pending');
            $table->string('uuid')->comment('transaction uuid');
            $table->foreignId('voucher_id')->nullable()->constrained();
            $table->string('ipn_id')->nullable()->comment('ipn id'); // from pesapal
            $table->string('order_tracking_id')->nullable()->comment('url for callback'); // from pesapal
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
