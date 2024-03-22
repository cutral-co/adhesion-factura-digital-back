<?php

namespace App\Http\Controllers;

use App\Http\Resources\SolicitudResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Mail\EmailAprobacion;
use App\Mail\EmailConfirmacion;
use App\Mail\EmailRechazo;
use App\Models\Solicitud;

class SolicitudController extends Controller
{
    public function index(Request $request)
    {
        $solicitudes = Solicitud::all();
        return sendResponse($solicitudes);
    }

    public function get_by_token(Request $request)
    {
        $solicitud = Solicitud::where('token_verificacion', $request->token)->first();
        if ($solicitud) {
            return sendResponse(new SolicitudResource($solicitud));
        }
        return sendResponse(null, 'No se encontro la solicitud', 404);
    }

    public function pendientes()
    {
        $solicitudes = Solicitud::whereNotNull('fecha_verificado')->where('estado_id', 1)->get();
        return sendResponse(SolicitudResource::collection($solicitudes));
    }

    public function aprobadas()
    {
        $solicitudes = Solicitud::where('estado_id', 2)->get();
        return sendResponse(SolicitudResource::collection($solicitudes));
    }

    public function rechazadas()
    {
        $solicitudes = Solicitud::where('estado_id', 3)->get();
        return sendResponse(SolicitudResource::collection($solicitudes));
    }

    public function cambiarEstado(Request $request)
    {
        $solicitud = Solicitud::find($request->id);
        if ($solicitud->estado_id == 2 || $solicitud->estado_id == 3) {
            return sendResponse(null, 'No se puede cambiar el estado de esta solicitud');
        }

        $solicitud->estado_id = $request->estado_id;
        $solicitud->save();

        /* confirmada */
        if ($solicitud->estado_id == 2) {
            Mail::to($solicitud->email)->send(new EmailAprobacion());
        }

        /* rechazada */
        if ($solicitud->estado_id == 3) {
            Mail::to($solicitud->email)->send(new EmailRechazo());
        }
        return sendResponse(new SolicitudResource($solicitud));
    }

    public function store(Request $request)
    {
        $body = $request->all();

        $cuit = $request->cuit;

        $solicitud = Solicitud::where('cuit', $cuit)->where('estado_id', 2)->first();
        if ($solicitud) {
            return sendResponse(null, "Número de CUIT/CUIL ya se encuentra adherido a la factura digital");
        }

        $solicitud = Solicitud::where('cuit', $cuit)->whereNotNull('fecha_verificado')->where('estado_id', 1)->first();
        if ($solicitud) {
            return sendResponse(null, "Número de CUIT/CUIL ya tiene un correo electrónico activado");
        }

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

        /* if (!$solicitud) {
            return redirect('http://www.cutralco.gob.ar/');
        }

        if ($solicitud->fecha_verificado) {
            return redirect('http://www.cutralco.gob.ar/');
        } */

        $solicitud->fecha_verificado = \Carbon\Carbon::now();
        $solicitud->save();

        $path = env('APP_CLIENT_URL') . "#/adhesion/$solicitud->token_verificacion/";
        return redirect($path);
    }

    public function correoVerificado(Request $request)
    {


        return view('emailConfirmation');
    }
}
