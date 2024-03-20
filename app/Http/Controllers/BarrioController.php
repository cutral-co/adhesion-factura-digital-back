<?php

namespace App\Http\Controllers;

use App\Mail\EmailConfirmacion;
use App\Models\Barrio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BarrioController extends Controller
{
    public function index()
    {
        $barrios = Barrio::all();
        return sendResponse($barrios);
    }
    public function email()
    {
        Mail::to('gon.pineiro@gmail.com')->send(new EmailConfirmacion('https://google.com.ar'));
    }
}
