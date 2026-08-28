<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class LandingController extends Controller
{
    public function index()
    {
        // Mengambil data event beserta relasi pelatihannya untuk ditampilkan di landing page
        $events = Event::with('pelatihan')->latest()->get();

        return view('landing', compact('events'));
    }
}