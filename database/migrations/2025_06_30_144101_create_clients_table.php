<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_profile_id')->references('id')->on('company_profile')->onDelete('cascade');
            $table->string('client_name');
            $table->string('display_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            $table->string('client_type')->index();
            $table->string('client_tax_number')->nullable();
            $table->foreignId('billing_address')->nullable()->constrained('addressbook')->onDelete('set null'); // Assuming 'addressbook' table exists for storing addresses
            $table->foreignId('shipping_address')->nullable()->constrained('addressbook')->onDelete('set null'); // Assuming 'addressbook' table exists for storing addresses
            $table->tinyInteger('is_active')->default(0)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Temporarily disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Schema::dropIfExists('clients');

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
