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
        Schema::create('inputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();  
            $table->integer('cantidad');
            $table->integer('cantidad_restante');
            $table->decimal('compra_unitaria', 8, 2); // Campo para el precio unitario
            $table->decimal('iva', 8, 2); // Campo para el precio unitario
            $table->decimal('costo_unitario', 8, 2); // Campo para el precio unitario
            $table->dateTime('fecha_entrada')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('documento_referencia')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('input');
    }
};
