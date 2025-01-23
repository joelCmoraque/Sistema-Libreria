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
        Schema::connection('pgsql_second')->create('dim_times', function (Blueprint $table) {
            $table->id('time_key');
            $table->date('fecha');
            $table->integer('año');
            $table->integer('mes');
            $table->integer('dia');
            $table->integer('trimestre');
            $table->integer('dia_semana');
            $table->integer('semana_año');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dim_times');
    }
};
