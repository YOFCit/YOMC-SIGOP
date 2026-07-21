<?php

namespace App\Livewire;

use Livewire\Component;

class GlobalAlert extends Component
{
  public $message = '';
  public $type = 'success';
  public $show = false;

  protected $listeners = ['showAlert' => 'displayAlert'];

  public function displayAlert($message, $type = 'success')
  {
    $this->message = $message;
    $this->type = $type;
    $this->show = true;
  }

  public function hideAlert()
  {
    $this->show = false;
    $this->message = '';
  }

  public function render()
  {
    return view('livewire.global-alert');
  }
}
