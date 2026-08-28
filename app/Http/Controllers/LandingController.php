<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        // Mengembalikan file resources/views/landing.blade.php
        return view('landing');
    }
}