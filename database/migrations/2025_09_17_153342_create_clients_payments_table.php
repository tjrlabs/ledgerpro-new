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
        Schema::create('clients_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payments_board_id')->constrained('payments_board')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->float('cash_sales', 15, 2);
            $table->float('pre_gst_amount', 15, 2);
            $table->float('gst_amount', 15, 2);
            $table->float('tds', 15, 2)->default(0);
            $table->float('subtotal_amount', 15, 2);
            $table->float('previous_balance', 15, 2)->default(0);
            $table->float('total_amount', 15, 2);
            $table->float('paid_amount', 15, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients_payments');
    }
};
