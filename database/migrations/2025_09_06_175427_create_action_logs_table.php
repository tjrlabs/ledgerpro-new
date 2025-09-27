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
        Schema::create('action_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_profile_id')->constrained('company_profile')->onDelete('cascade');
            $table->enum('resource_type', ['employee', 'sale', 'expense', 'item', 'client']);
            $table->unsignedBigInteger('resource_id')->index();
            $table->enum('action', ['advance_paid', 'advance_cleared', 'salary_paid', 'item_price_updated']);
            $table->string('action_value')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('performed_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_logs');
    }
};
