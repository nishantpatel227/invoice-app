<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Middleware or other initializations can be added here
    }
}
