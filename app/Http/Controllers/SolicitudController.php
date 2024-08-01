<?php

namespace App\Http\Controllers;

use App\Http\Resources\SolicitudResource;
use App\Models\UserAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Mail, DB};

use App\Mail\EmailAprobacion;
use App\Mail\EmailConfirmacion;
use App\Mail\EmailPassword;
use App\Mail\EmailRechazo;
use App\Models\Person;
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

    public function no_verificadas()
    {
        $solicitudes = Solicitud::whereNull('fecha_verificado')->get();
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
            $this->createUser($solicitud);
            //Mail::to($solicitud->email)->send(new EmailAprobacion());
        }

        /* rechazada */
        if ($solicitud->estado_id == 3) {
            Mail::to($solicitud->email)->send(new EmailRechazo());
        }
        return sendResponse(new SolicitudResource($solicitud));
    }

    private function createUser($solicitud)
    {
        $_person = $solicitud->toArray();
        unset($_person['id']);

        $person = Person::create($_person);

        $password = \Illuminate\Support\Str::random(8);
        $hash = \Illuminate\Support\Facades\Hash::make($password);

        UserAdmin::create([
            'cuit' => $person->cuit,
            'password' => $hash,
            'person_id' => $person->id,
            'is_verified' => true,
        ]);

        Mail::to($person->email)->send(new EmailPassword($password, $person));
    }

    public function store(Request $request)
    {
        $body = $request->all();

        $cuit = $request->cuit;

        /* Cuando ya se encuentra adherido el CUIT, aprobado por administrador */
        $solicitud = Solicitud::where('cuit', $cuit)->where('estado_id', 2)->first();
        if ($solicitud) {
            return sendResponse(null, "Número de CUIT/CUIL ya se encuentra adherido a la factura digital");
        }

        /* Cuando existe un registro con correo activado, enviando CUIT que no se encuentra rechazado */
        $solicitud = Solicitud::where('cuit', $cuit)->whereNotNull('fecha_verificado')->where('estado_id', '!=', 3)->first();
        if ($solicitud) {
            return sendResponse(null, "Número de CUIT/CUIL ya tiene un correo electrónico activado");
        }

        /* Cuando existe un registro con correo activado, enviando correo que no se encuentra rechazado */
        $solicitud = Solicitud::where('email', $request->email)->whereNotNull('fecha_verificado')->where('estado_id', '!=', 3)->first();
        if ($solicitud) {
            return sendResponse(null, "La cuenta de correo $request->email ya se encuentra registrada");
        }

        $body['token_verificacion'] = uniqid();
        $solicitud = Solicitud::create($body);

        try {
            $link = env('APP_URL') . "verificar-correo?token=$solicitud->token_verificacion";
            Mail::to($solicitud->email)->send(new EmailConfirmacion($link));
            $solicitud->ultimo_envio_email = \Carbon\Carbon::now();
            $solicitud->save();
        } catch (\Throwable $th) {
            //throw $th;
        }

        return sendResponse($solicitud);
    }

    public function destroy(Request $request)
    {
        $model = Solicitud::find($request->id);
        $model->delete();

        return sendResponse('Recurso eliminado');
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

        $path = env('APP_CLIENT_URL') . "#/adhesion/$solicitud->token_verificacion/";
        return redirect($path);
    }

    public function correoVerificado(Request $request)
    {
        return view('emailConfirmation');
    }

    public function envio_correo_verificar()
    {
        $dosDiasAtras = \Carbon\Carbon::now()->subDays(2);

        $solicitudd = Solicitud::where('ultimo_envio_email', '<=', $dosDiasAtras)
            ->whereNull('fecha_verificado')
            ->get();

        foreach ($solicitudd as $solicitud) {
            $link = env('APP_URL') . "verificar-correo?token=$solicitud->token_verificacion";
            Mail::to($solicitud->email)->send(new EmailConfirmacion($link));
            $solicitud->ultimo_envio_email = \Carbon\Carbon::now();
            $solicitud->save();
        }
        return redirect()->away('http://www.cutralco.gob.ar/');
    }
}
