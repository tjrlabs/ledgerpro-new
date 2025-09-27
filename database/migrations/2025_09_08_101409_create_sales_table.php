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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_profile_id')->constrained('company_profile')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->date('sale_date')->index();
            $table->enum('sales_type', ['cash', 'invoice'])->index();
            $table->decimal('base_amount', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('tax_rate', 5, 2)->default(0.00); // Tax rate as a percentage
            $table->decimal('total_amount', 10, 2);
            $table->decimal('tds', 10, 2)->default(0.00); // Tax Deducted at Source
            $table->decimal('tds_rate', 5, 2)->default(0.00); // TDS rate as a percentage;
            $table->date('due_date')->nullable()->index();
            $table->tinyInteger('paid')->default(0)->index();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('sales');
        Schema::enableForeignKeyConstraints();
    }
};
