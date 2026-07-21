<?php

namespace App\Http\Controllers;

class ControllerMovimientos extends Controller
{
  public function index()
  {
    return view('Containers.ConMovimientos');
  }
}
