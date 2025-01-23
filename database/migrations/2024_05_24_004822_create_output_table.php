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
        Schema::create('outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 8, 2); // Campo para el precio unitario
            $table->decimal('total', 8, 2);
            $table->dateTime('fecha_salida')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('documento_referencia')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('output');
    }
};
