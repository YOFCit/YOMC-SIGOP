<?php

namespace App\Http\Controllers;

class ControllerUsuarios extends Controller
{
  public function index()
  {
    return view('Containers.ConUsuarios');
  }
}
