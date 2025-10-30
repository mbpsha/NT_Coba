<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TokoController extends Controller
{
    public function toko()
    {
        return Inertia::render('User/Toko');
    }
}
