<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Session;
use Ably;
use App\Editorial;
use App\Cabezote;

use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->session()->forget('pregamereto');
        $request->session()->forget('pregameMachine');

        return view('home');
    }

    public function screen(Request $request)
    {
        $verificar = $this->generarFormVerification();
        $request->session()->put('verificar_form', $verificar['valor']);

        $cabezotes = Cabezote::orderBy('orden')->get();

        return view('screen', [
            'data' => $this->getTextosEditorial(),
            'cabezotes' => $cabezotes,
            'verificar' => $verificar['texto'],
        ]);
    }

    public function indexEdit()
    {
        return view('index_edit', [
            'data' => $this->getTextosEditorial(),
        ]);
    }

    public function saveEditorial(Request $request)
    {
        Editorial::where('id', $request->input('id'))->update([
            'texto' => $request->input('html'),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'actualizado',
        ]);
    }


    public function defaultEditorial(Request $request)
    {
        Editorial::where('id', $request->input('id'))->update([
            'default' => $request->input('html'),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'actualizado',
        ]);
    }

    public function resetEditorial(Request $request)
    {
        $editorial = Editorial::where('id', $request->input('id'))->first();

        return response()->json([
            'status' => 200,
            'message' => 'actualizado',
            'html' => $editorial->reset,
        ]);
    }

    protected function getTextosEditorial()
    {
        $data = [];
        $textos = Editorial::where('activo', 1)->orderBy('orden')->get();

        foreach ($textos as $key => $row) {
            $data[$row['nombre']] = $row;
        }

        return $data;
    }

    public function politica()
    {
        return view('politica');
    }


    protected function generarFormVerification()
    {
        $valor1 = rand(0, 10);
        $valor2 = rand(0, 10);
        $securitytext = "¿Cuanto es {$valor1} + {$valor2}?";
        $resultadoSuma = (int)$valor1 + (int) $valor2;

        return [
            'valor' => $resultadoSuma,
            'texto' => $securitytext,
        ];
    }

    public function authAbly()
    {
        $aTokenParams = [
            'capability' => [
                'damos' => ['publish', 'subscribe']
            ],
            // 'clientId' => strval(time())
            'clientId' => 'diegoprueba'
        ];

        // return Ably::auth()->requestToken([ 'clientId' => 'client123', ]);
        return response()->json(Ably::auth()->createTokenRequest($aTokenParams)->toArray());
    }
}
