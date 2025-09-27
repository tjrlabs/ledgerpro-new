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
        Schema::create('accounts_balance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_profile_id')->constrained('company_profile')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade
');
            $table->integer('month')->index();
            $table->integer('year')->index();
            $table->decimal('opening_balance', 15, 2)->default(0.00);
            $table->unique(['company_profile_id', 'client_id', 'month', 'year'], 'unique_account_balance');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_balance');
    }
};
