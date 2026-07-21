<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class Livlogin extends Component
{
  // Control de vistas

  public $viewMode = 'login';

  // LOGIN

  public $NumeroEmpleado;
  public $password;

  // RESET PASSWORD

  public $resetEmpleado;
  public $newPassword;
  public $confirmPassword;

  protected function rules()
  {
    if ($this->viewMode == 'login') {

      return [

        'NumeroEmpleado' => 'required|integer',
        'password' => 'required|min:4',

      ];
    }

    if ($this->viewMode == 'reset') {

      return [

        'resetEmpleado' => 'required|integer|exists:empleados,NumeroEmpleado',
        'newPassword' => 'required|min:4|same:confirmPassword',
        'confirmPassword' => 'required',

      ];
    }
  }

  // LOGIN

  public function login()
  {
    $this->validate();

    $credentials = [

      'NumeroEmpleado' => $this->NumeroEmpleado,
      'password' => $this->password
    ];
    if (Auth::attempt($credentials)) {
      session()->regenerate();
      return redirect()->route('Home');
    }
    $this->dispatch('showAlert', 'Credenciales incorrectas', 'danger');
  }

  // RESET PASSWORD
  public function resetPassword()
  {
    $this->validate();
    $user = User::where(
      'NumeroEmpleado',
      $this->resetEmpleado
    )->first();
    if ($user) {
      $user->password = Hash::make($this->newPassword);
      $user->save();
      $this->dispatch('showAlert', 'Contraseña actualizada correctamente', 'success');
      $this->viewMode = 'login';
      $this->reset([
        'resetEmpleado',
        'newPassword',
        'confirmPassword'
      ]);
    }
  }

  // CAMBIAR A RESET
  public function showReset()
  {
    $this->resetErrorBag();
    $this->viewMode = 'reset';
  }

  // VOLVER A LOGIN
  public function showLogin()
  {
    $this->resetErrorBag();
    $this->viewMode = 'login';
  }

  public function render()
  {
    return view('livewire.livlogin');
  }
}
