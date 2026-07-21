<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('areas', function (Blueprint $table) {

      $table->id('IdArea');

      $table->string('Nombre', 100)->unique();

      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('areas');
  }
};
