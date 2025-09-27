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
        Schema::create('company_profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('company_name');
            $table->string('company_email')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_website')->nullable();
            $table->foreignId('logo')->nullable()->constrained('media')->onDelete('set null'); // Assuming 'media' table exists for storing logos
            $table->foreignId('billing_address')->nullable()->constrained('addressbook')->onDelete('set null'); // Assuming 'addresses' table exists for storing company addresses
            $table->foreignId('shipping_address')->nullable()->constrained('addressbook')->onDelete('set null'); // Assuming 'addresses' table exists for storing company addresses
            $table->tinyInteger('is_default')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('company_profile');
        Schema::enableForeignKeyConstraints();
    }
};
