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
            $table->foreignId('company_profile_id')->constrained('company_profile')->onDelete('cascade');
            $table->uuid('uuid');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('cascade');
            $table->enum('transaction_type', ['sale', 'payment', 'expense'])->index();
            $table->date('transaction_date')->index();
            $table->enum('sales_type', ['cash', 'invoice'])->nullable()->index();
            $table->decimal('base_amount', 10, 2)->nullable();
            $table->decimal('tax_amount', 10, 2)->default(0.00)->nullable();
            $table->decimal('tax_rate', 5, 2)->default(0.00)->nullable(); // Tax rate as a percentage
            $table->decimal('tds', 10, 2)->default(0.00)->nullable(); // Tax Deducted at Source
            $table->decimal('tds_rate', 5, 2)->default(0.00)->nullable(); // TDS rate as a percentage;
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->date('due_date')->nullable()->index();
            $table->tinyInteger('paid')->default(0)->nullable()->index();
            $table->foreignId('payment_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'cash_transfer'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
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
