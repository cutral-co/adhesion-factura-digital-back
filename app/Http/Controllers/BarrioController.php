<?php

namespace App\Http\Controllers;

use App\Models\BarrioMunicipio;

class BarrioController extends Controller
{
    public function index()
    {
        $barrios = BarrioMunicipio::all();
        return sendResponse($barrios);
    }
}
