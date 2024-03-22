<?php

namespace App\Http\Controllers;

use App\Models\Barrio;

class BarrioController extends Controller
{
    public function index()
    {
        $barrios = Barrio::all();
        return sendResponse($barrios);
    }
}
