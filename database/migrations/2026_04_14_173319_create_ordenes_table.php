<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::create('ordenes', function (Blueprint $table) {

      $table->id('IdOrden');

      $table->string('Folio', 100)->unique();
      $table->text('Procedimiento')->nullable();
      $table->text('Descripcion')->nullable();
      $table->datetime('Timestamp')->nullable();

      // Materiales
      $table->boolean('ReqMaterial')->default(false);

      // Paro de línea
      $table->boolean('ParoLinea')->default(false);
      $table->datetime('TiempoSolucion')->nullable();

      // Responsables
      $table->integer('NumeroEmpleado');
      $table->string('Maquina', 255)->nullable();

      // Ubicación
      $table->unsignedBigInteger('IdArea');
      $table->unsignedBigInteger('IdLinea');

      // Estado y tiempos
      $table->enum('Status', ['abierta', 'en_proceso', 'cerrada'])->default('abierta');
      $table->enum('Tipo', ['correctivo', 'mejora', 'instalación', 'Otro', 'NE'])->default('NE');
      $table->string('Otro', 255)->nullable();
      $table->dateTime('HoraApertura')->nullable();
      $table->dateTime('HoraCierre')->nullable();

      // ========== NUEVAS COLUMNAS ==========
      $table->dateTime('HoraRecepcionLinea')->nullable()->after('HoraCierre');
      $table->dateTime('HoraArranque')->nullable()->after('HoraRecepcionLinea');
      $table->string('DescripcionArranque', 255)->nullable();
      // =====================================

      $table->timestamps();

      // FOREIGN KEYS
      $table->foreign('NumeroEmpleado')
        ->references('NumeroEmpleado')
        ->on('empleados')
        ->onDelete('restrict');

      $table->foreign('IdArea')
        ->references('IdArea')
        ->on('areas')
        ->onDelete('restrict');

      $table->foreign('IdLinea')
        ->references('IdLinea')
        ->on('lineas')
        ->onDelete('restrict');

      // ÍNDICES
      $table->index('Folio');
      $table->index('NumeroEmpleado');
      $table->index('IdArea');
      $table->index('IdLinea');
      $table->index('Timestamp');
      $table->index('Status');
    });
  }

  public function down()
  {
    Schema::dropIfExists('ordenes');
  }
};
