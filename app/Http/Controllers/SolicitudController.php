<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = Solicitud::all();
        return sendResponse($solicitudes);
    }

    public function store(Request $request)
    {
        $solicitudes = Solicitud::create($request->all());
        return sendResponse($solicitudes);
    }
}
