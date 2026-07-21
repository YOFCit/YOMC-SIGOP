<?php

namespace App\Http\Controllers;

class ControllerOrdenes extends Controller
{
  public function index()
  {
    return view('Containers.ConOrdenes');
  }
}
