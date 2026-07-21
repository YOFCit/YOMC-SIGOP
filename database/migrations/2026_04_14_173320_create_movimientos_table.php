<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::create('movimientos', function (Blueprint $table) {

      $table->id('IdMovimiento');

      $table->string('TipoMovimiento');

      $table->decimal('CantidadUsada', 10, 2);

      $table->unsignedBigInteger('IdOrden');

      $table->unsignedBigInteger('IdMaterial');

      $table->timestamps();

      // FK Orden

      $table->foreign('IdOrden')
        ->references('IdOrden')
        ->on('ordenes')
        ->onDelete('cascade');

      // FK Material

      $table->foreign('IdMaterial')
        ->references('IdMaterial')
        ->on('materiales')
        ->onDelete('restrict');
    });
  }

  public function down()
  {
    Schema::dropIfExists('movimientos');
  }
};
