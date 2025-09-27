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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_profile_id')->constrained('company_profile')->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null'); // Assuming 'clients' table exists for storing client information
            $table->string('item_type');
            $table->string('item_name')->nullable();
            $table->string('item_description')->nullable();
            $table->string('item_sku')->nullable();
            $table->decimal('item_price', 10, 2)->default(0.00);
            $table->string('item_unit')->default('pcs');
            $table->string('item_hsn_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('products');
        Schema::enableForeignKeyConstraints();
    }
};
