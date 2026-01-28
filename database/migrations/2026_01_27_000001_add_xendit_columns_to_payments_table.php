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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_gateway')->default('xendit')->after('payment_channel');
            $table->string('xendit_invoice_id')->nullable()->after('midtrans_response');
            $table->string('xendit_invoice_url')->nullable()->after('xendit_invoice_id');
            $table->string('xendit_payment_id')->nullable()->after('xendit_invoice_url');
            $table->string('xendit_va_id')->nullable()->after('xendit_payment_id');
            $table->string('va_number')->nullable()->after('xendit_va_id');
            $table->string('va_bank')->nullable()->after('va_number');
            $table->json('xendit_response')->nullable()->after('va_bank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_gateway',
                'xendit_invoice_id',
                'xendit_invoice_url',
                'xendit_payment_id',
                'xendit_va_id',
                'va_number',
                'va_bank',
                'xendit_response',
            ]);
        });
    }
};
