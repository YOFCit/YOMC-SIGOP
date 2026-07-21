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
    Schema::create('tiempos_extra', function (Blueprint $table) {
      $table->id();

      $table->string('NumeroEmpleado');
      $table->string('Nombre');
      $table->string('Departamento');
      $table->string('TrabajarA')->nullable();
      $table->time('HoraInicio')->nullable();
      $table->time('HoraFin')->nullable();
      $table->decimal('HorasExtra', 5, 2)->nullable();
      $table->text('Descripcion')->nullable();
      $table->text('Causas')->nullable();
      $table->string('Solicitante');
      $table->date('FechaSolicitud');
      $table->string('Autorizador')->nullable();
      $table->string('Estatus')->default('Pendiente');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('tiempos_extra');
  }
};
