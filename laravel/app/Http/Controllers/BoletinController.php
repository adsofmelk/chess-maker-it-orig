<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ConfirmSubscription;
use App\Mail\CreateSubscription;
use App\Boletin;

use Illuminate\Support\Facades\Mail;

class BoletinController extends Controller
{
    public function list(Request $request)
    {
        return view('panel.boletin.index');
    }

    public function formIndex(Request $request)
    {
        if ($request->input('verificar') != $request->session()->get('verificar_form')) {
            return response()->json([
                'status' => 304,
                'message' => 'La suma es incorrecta',
            ]);
        }

        $email = $request->input('email');

        $boletin = Boletin::where('email', $email)->first();

        if ($boletin != null) {
            return response()->json([
                'status' => 304,
                'message' => 'Ya se ha suscrito el email al boletín.',
            ]);
        }

        $token = str_random(50);
        $nombre = $request->input('nombre');

        Boletin::create([
            'email' => $email,
            'name' => $nombre,
            'token' => $token,
        ]);

        $data = [
            'greeting' => 'Hola '.$nombre,
            'url' => route('confirm_email', ['token'=> $token]),
        ];

        Mail::to($email)->send(new ConfirmSubscription($data));

        $request->session()->forget('verificar_form');

        return response()->json([
            'status' => 200,
            'message' => 'Por favor revise su correo para confirmar la suscripción.',
            'verificar' => $request->input('verificar'),
        ]);
    }

    public function confirmEmail(Request $request, $token)
    {
        $boletin = Boletin::where('token', $token)->where('activo', '0')->first();

        if ($boletin == null) {
            return view('errors.404');
        }

        $token = str_random(50);

        $boletin->activo = 1;
        $boletin->token = $token;
        $boletin->update();

        $data = [
            'greeting' => 'Hola '.$boletin->name,
            'url' => route('desuscribirse', ['token'=> $token]),
        ];

        Mail::to($boletin->email)->send(new CreateSubscription($data));

        return view('alerts.notify', [
            'message' => 'Se ha suscrito correctamente al boletín.',
        ]);
    }

    public function desuscribirse(Request $request, $token)
    {
        $boletin = Boletin::where('token', $token)->where('activo', '1')->first();

        if ($boletin == null) {
            return view('errors.404');
        }

        $boletin->delete();

        return view('alerts.notify', [
            'message' => 'Te has dado de baja correctamente en el boletín.',
        ]);
    }

    public function alljson()
    {
        $boletines = Boletin::select('name', 'email', 'created_at', 'id')->where('activo', 1)->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 200,
            'data' => $boletines,
        ]);
    }
}
