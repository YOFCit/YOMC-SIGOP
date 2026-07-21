<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::create('materiales', function (Blueprint $table) {

      $table->id('IdMaterial');

      $table->string('Nombre');

      $table->text('Descripcion')->nullable();

      $table->string('Location')->nullable();

      $table->integer('Stock')->default(0);

      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('materiales');
  }
};
