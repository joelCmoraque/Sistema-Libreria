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
        Schema::connection('pgsql_second')->create('dim_products', function (Blueprint $table) {
            $table->id('product_key');
            $table->integer('product_id');
            $table->string('codigo_unico')->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('unidad_medida');
            $table->integer('category_id');
            $table->string('category_nombre');
            $table->integer('brand_id');
            $table->string('brand_nombre');
            $table->integer('provider_id');
            $table->string('provider_nombre');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dim_products');
    }
};
