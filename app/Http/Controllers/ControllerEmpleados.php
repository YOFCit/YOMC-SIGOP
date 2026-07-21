<?php

namespace App\Http\Controllers;

class ControllerEmpleados extends Controller
{
  public function index()
  {
    return view('Containers\ConEmpleados');
  }
}
