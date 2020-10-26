<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

use App\User;

class UserController extends Controller
{
    public function list()
    {
        return  view('panel.usuarios.index');
    }

    public function add()
    {
        // return  view('panel.distribuciones.agrega');
    }

    public function edit(Request $request, $id)
    {
        // $distribution = Distri::where('id', $id)->firstOrFail();

        // return view('panel.distribuciones.edit', [
        //     'distribution' => $distribution,
        // ]);
    }

    public function create(Request $request)
    {
        // $newdistri = Distri::create([
        //     'distribution'=> $request->input('distribution'),
        // ]);

        // $request->session()->flash('alert', 'El registro ha sido creado!');

        // return response()->json([
        //     'status' => 200,
        //     'url' => route('distri_edit', ['id' => $newdistri->id]),
        // ]);
    }

    public function update(Request $request)
    {
        // Distri::where('id', $request->input('id'))->update([
        //     'distribution'=> $request->input('distribution'),
        // ]);

        // return response()->json([
        //     'status' => 200,
        //     'message' => 'Distribución actualizada!',
        // ]);
    }

    public function delete(Request $request)
    {
        // Distri::destroy($request->input('id'));

        // return response()->json([
        //     "status" => 200,
        //     'message' => 'Registro eliminado',
        // ]);
    }

    public function show(Request $request)
    {
        Carbon::setLocale('es');

        $usuario = User::select('id', 'name', 'rating', 'game_count', 'game_win', 'game_empates', 'game_lose', 'type_auth', 'email', 'created_at', 'avatar')->where('id', $request->input('id'))->first();

        return view('panel.usuarios.show', [
            'usuario' => $usuario,
        ]);
    }

    public function alljson()
    {
        $users = User::select('id', 'name', 'rating', 'game_count', 'game_win', 'game_empates', 'game_lose', 'type_auth', 'email', 'created_at')->get();

        return response()->json([
            'status' => 200,
            'data' => $users,
        ]);
    }
}
