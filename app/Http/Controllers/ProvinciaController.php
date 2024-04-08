<?php

namespace App\Http\Controllers;

use App\Models\Provincia;

class ProvinciaController extends Controller
{
    public function index()
    {
        $provincias = Provincia::all();
        return sendResponse($provincias);
    }
}
