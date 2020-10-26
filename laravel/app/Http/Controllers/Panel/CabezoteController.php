<?php

namespace App\Http\Controllers\Panel;

use Intervention\Image\ImageManager as Image;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Traits\Slim as Slim;

use App\Cabezote;

// require_once('../../slim/slim.php');

class CabezoteController extends Controller
{
    public function list()
    {
        return  view('panel.cabezotes.index');
    }

    public function add()
    {
        return view('panel.cabezotes.agrega');
    }

    public function edit(Request $request, $id)
    {
        $cabezote = Cabezote::where('id', $id)->first();

        return view('panel.cabezotes.edit', [
            'cabezote' => $cabezote,
        ]);
    }

    public function create(Request $request)
    {
        $images = Slim::getImages('foto');
        $nuevonombre = null;

        if ($images != false) {
            $directorio = public_path().'/cabezotes/';

            if (!file_exists($directorio)) {
                mkdir($directorio);
            }

            $sExtension = pathinfo($images[0]['output']['name'], PATHINFO_EXTENSION);

            $nuevonombre = 'slide_'.time().'.'.$sExtension;

            $output = Slim::saveFile($images[0]['output']['data'], $nuevonombre, $directorio, false);

            $imageInter = new Image();
            $img = $imageInter->make($directorio.$nuevonombre);
            $img->resize(1920, 1080)->save($directorio.$nuevonombre, 90);
        }

        $newcabezote = Cabezote::create([
            'titulo'=> $request->input('titulo'),
            'resumen'=> $request->input('resumen'),
            'texto_boton'=> $request->input('texto_boton'),
            'enlace_boton'=> $request->input('enlace_boton'),
            'foto' => $nuevonombre,
        ]);

        // return response()->redirect('/panel/cabezotes/edit/'.$newcabezote->id);
        return redirect()->route('cabezotes_edit', ['id' => $newcabezote->id])->with('alert', 'El registro ha sido creado!');
    }

    public function update(Request $request)
    {
        $images = Slim::getImages('foto');
        $nuevonombre = $request->input('name_photo_old');

        if ($images != false) {
            $directorio = public_path().'/cabezotes/';

            if (!file_exists($directorio)) {
                mkdir($directorio);
            }

            if (file_exists($directorio.$nuevonombre)) {
                unlink($directorio.$nuevonombre);
            }

            $sExtension = pathinfo($images[0]['output']['name'], PATHINFO_EXTENSION);

            $nuevonombre = 'slide_'.time().'.'.$sExtension;

            $output = Slim::saveFile($images[0]['output']['data'], $nuevonombre, $directorio, false);

            $imageInter = new Image();
            $img = $imageInter->make($directorio.$nuevonombre);
            $img->resize(1920, 1080)->save($directorio.$nuevonombre, 90);
        }

        Cabezote::where('id', $request->input('id'))->update([
            'titulo'=> $request->input('titulo'),
            'resumen'=> $request->input('resumen'),
            'texto_boton'=> $request->input('texto_boton'),
            'enlace_boton'=> $request->input('enlace_boton'),
            'foto' => $nuevonombre,
        ]);

        // return response()->redirect('/panel/cabezotes/edit/'.$newcabezote->id);
        return redirect()->route('cabezotes_edit', ['id' => $request->input('id')])->with('alert', 'Registro actualizado!');
    }

    public function delete(Request $request)
    {
        Cabezote::destroy($request->input('id'));

        $nuevonombre = $request->input('foto');
        if ($nuevonombre!= null) {
            $directorio = public_path().'/cabezotes/';

            if (!file_exists($directorio)) {
                mkdir($directorio);
            }

            if (file_exists($directorio.$nuevonombre)) {
                unlink($directorio.$nuevonombre);
            }
        }

        return response()->json([
            "status" => 200,
            'message' => 'Registro eliminado',
        ]);
    }

    public function order(Request $request)
    {
        $aRows = $request->input("aRegistros");

        if (is_array($aRows)) {
            $aRowsOrder = $aRows;
            $pos = 1;

            foreach ($aRowsOrder as $key) {
                DB::update('update cabezotes set orden = ? where id = ?', [$pos, $key]);
                $pos++;
            }

            return response()->json([
                "status" => 200,
                'message' => 'Registros ordenados',
            ]);
        }

        return response()->json([
            'status' => 500,
            'message' => 'Ha ocurrido un error',
        ]);
    }

    public function alljson()
    {
        $cabezotes = Cabezote::orderBy('orden')->get();

        return response()->json([
            'status' => 200,
            'data' => $cabezotes,
        ]);
    }
}
