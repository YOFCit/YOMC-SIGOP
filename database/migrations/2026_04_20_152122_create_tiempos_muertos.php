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
    Schema::create('tiempos_muertos', function (Blueprint $table) {
      $table->id();

      // Supervisores o jefes de área
      $table->string('Name');
      $table->string('Departament');
      $table->decimal('EmployeeID', 8, 0, true);
      $table->string('Area');
      $table->string('ProductionLine');
      $table->string('Description');
      $table->dateTime('DateOfOpen');

      // Campos para MTTO o Admin
      $table->boolean('Status');
      $table->string('SolutionDescription')->nullable();
      $table->dateTime('DateOfClose')->nullable();

      // Tiempo de reparación
      $table->string('TimeUsed')->nullable();

      // Campo sin relación foránea
      $table->unsignedBigInteger('created_by')->nullable();
      $table->string('user_departament')->nullable();

      // Fechas
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('tiempos_muertos');
  }
};
