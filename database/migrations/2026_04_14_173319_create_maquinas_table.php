<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('maquinas', function (Blueprint $table) {

      $table->id('IdMaquina');

      $table->string('Nombre', 100);

      $table->foreignId('IdLinea')
        ->constrained('lineas', 'IdLinea')
        ->cascadeOnDelete();

      $table->timestamps();

      // Evita máquinas duplicadas dentro de una misma línea
      $table->unique(['Nombre', 'IdLinea']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('maquinas');
  }
};
