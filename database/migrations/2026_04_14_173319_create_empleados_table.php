<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::create('empleados', function (Blueprint $table) {

      $table->integer('NumeroEmpleado')->primary();

      $table->string('Nombre');

       $table->string('Email');

      $table->string('Position')->nullable();

      $table->string('Departamento')->nullable();

      $table->string('password');

      $table->rememberToken();

      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('empleados');
  }
};
