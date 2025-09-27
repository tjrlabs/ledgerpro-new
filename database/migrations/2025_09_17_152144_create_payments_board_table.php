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
        Schema::create('payments_board', function (Blueprint $table) {
            $table->id();
            $table->string('board_month_year');
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->integer('total_days');
            $table->integer('clients_count');
            $table->float('total_pre_gst_amount', 15, 2);
            $table->float('total_gst_amount', 15, 2);
            $table->float('total_cash_sales', 15, 2);
            $table->float('total_tds', 15, 2);
            $table->float('total_previous_balance', 15, 2);
            $table->float('total_amount', 15, 2);
            $table->float('total_net_amount', 15, 2);
            $table->float('total_paid_amount', 15, 2);
            $table->float('total_unpaid_amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('payments_board');
        Schema::enableForeignKeyConstraints();
    }
};
