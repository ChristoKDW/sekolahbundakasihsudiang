<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->string('order_id')->unique();
            $table->foreignId('bill_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->string('payment_channel')->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'expired', 'refunded'])->default('pending');
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('midtrans_order_id')->nullable();
            $table->json('midtrans_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique();
            $table->date('reconciliation_date');
            $table->integer('total_transactions');
            $table->integer('matched_transactions');
            $table->integer('unmatched_transactions');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('matched_amount', 15, 2);
            $table->decimal('unmatched_amount', 15, 2);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->json('details')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('reconciliation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reconciliation_id')->constrained('payment_reconciliations')->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
            $table->string('midtrans_transaction_id');
            $table->string('midtrans_order_id');
            $table->decimal('midtrans_amount', 15, 2);
            $table->decimal('system_amount', 15, 2)->nullable();
            $table->enum('match_status', ['matched', 'amount_mismatch', 'not_found', 'duplicate'])->default('not_found');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reconciliation_items');
        Schema::dropIfExists('payment_reconciliations');
        Schema::dropIfExists('payments');
    }
};
