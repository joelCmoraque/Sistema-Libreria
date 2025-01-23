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
        Schema::connection('pgsql_second')->create('fact_transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->unsignedBigInteger('product_key');
            $table->unsignedBigInteger('deposit_key');
            $table->unsignedBigInteger('time_key');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->enum('transaction_type', ['input', 'output']);
            $table->string('reference_document')->nullable();
            $table->timestamps();

            $table->foreign('product_key')->references('product_key')->on('dim_products');
            $table->foreign('deposit_key')->references('deposit_key')->on('dim_deposits');
            $table->foreign('time_key')->references('time_key')->on('dim_times');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fact_transactions');
    }
};
