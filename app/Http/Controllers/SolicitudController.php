<?php

namespace App\Http\Controllers;

use App\Mail\EmailConfirmacion;
use App\Mail\EmailPostConfirmacion;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = Solicitud::all();
        return sendResponse($solicitudes);
    }

    public function store(Request $request)
    {
        $body = $request->all();
        $body['token_verificacion'] = uniqid();
        $solicitud = Solicitud::create($body);

        $link = env('APP_URL') . "verificar-correo?token=$solicitud->token_verificacion";

        try {
            Mail::to($solicitud->email)->send(new EmailConfirmacion($link));
            $solicitud->ultimo_envio_email = \Carbon\Carbon::now();
            $solicitud->save();
        } catch (\Throwable $th) {
            //throw $th;
        }

        return sendResponse($solicitud);
    }

    public function verificarCorreo(Request $request)
    {
        $token = $request->all();
        $solicitud = Solicitud::where('token_verificacion', $token)->first();

        if (!$solicitud) {
            return redirect('http://www.cutralco.gob.ar/');
        }

        if ($solicitud->fecha_verificado) {
            return redirect('http://www.cutralco.gob.ar/');
        }

        $solicitud->fecha_verificado = \Carbon\Carbon::now();
        $solicitud->save();

        Mail::to($solicitud->email)->send(new EmailPostConfirmacion());

        return redirect('http://www.cutralco.gob.ar/');
    }
}
