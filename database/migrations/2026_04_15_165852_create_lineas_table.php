<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('lineas', function (Blueprint $table) {

      $table->id('IdLinea');

      $table->string('Nombre', 100);

      $table->foreignId('IdArea')
        ->constrained('areas', 'IdArea')
        ->cascadeOnDelete();

      $table->timestamps();

      // Evita líneas duplicadas dentro de una misma área
      $table->unique(['Nombre', 'IdArea']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('lineas');
  }
};
