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
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->default(null);
            $table->string('amount')->nullable()->default(null);
            $table->string('currency')->nullable()->default(null);
            $table->string('confirmation_code')->nullable()->default(null);
            $table->timestamp('paid_at')->nullable()->default(null);
            $table->string('payment_account')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            //
        });
    }
};
