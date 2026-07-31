<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('global_updates', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('version')->default(1);
      $table->timestamps();
    });

    DB::table('global_updates')->insert([
      'version' => 1,
      'created_at' => now(),
      'updated_at' => now(),
    ]);
  }

  public function down(): void
  {
    Schema::dropIfExists('global_updates');
  }
};
