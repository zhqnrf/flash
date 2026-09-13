<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class LandingController extends Controller
{
    public function index()
    {
        // Mengambil maksimal 4 data event terbaru beserta relasinya
        $events = Event::with('pelatihan')->latest()->take(4)->get();

        return view('landing', compact('events'));
    }
    public function validasiEvent($uuid)
{
    $event = \App\Models\Event::where('uuid', $uuid)->firstOrFail();
    return view('public.event.validasi', compact('event'));
}
}