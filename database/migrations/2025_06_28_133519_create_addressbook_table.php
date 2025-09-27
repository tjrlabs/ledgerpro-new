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
        Schema::create('addressbook', function (Blueprint $table) {
            $table->id();
            $table->string('street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country_code', 3)->nullable(); // ISO 3166-1 alpha-3 code
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable foreign key checks to avoid issues during rollback
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('addressbook');
        Schema::enableForeignKeyConstraints();
    }
};
