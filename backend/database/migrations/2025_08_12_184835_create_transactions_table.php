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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('employee_id')->constrained();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('service_charge', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->enum('payment_method', ['cash', 'card', 'digital_wallet']);
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
            $table->timestamp('transaction_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};