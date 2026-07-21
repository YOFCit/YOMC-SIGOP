<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    User::create([
      'NumeroEmpleado' => 8300109,
      'Nombre' => 'Samuel de Jesus Martin Munoz',
      'Email'=> 'samuel.martin@yofc.com',
      'Position' => 'Administrador',
      'Departamento' => 'IT',
      'password' => Hash::make('admin123'),
    ]);
  }
}
