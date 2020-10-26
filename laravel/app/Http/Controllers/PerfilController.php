<?php

namespace App\Http\Controllers;

use App\GamesTemp;
use Illuminate\Http\Request;
use App\Traits\Slim as Slim;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager as Image;
use Illuminate\Support\Facades\DB;

use App\HallsWait as HallsWait;
use App\Partidas as Game;
use App\GamesTemp as PreGame;
use App\SessionesAbly;
use App\User;

class PerfilController extends Controller
{
    public function index()
    {
        return view('perfil.index');
    }
    
    public function changePassword(Request $request)
    {
        $password = $request->input('passant');
        $passwordNew = $request->input('passnew');
        $passwordRepeat = $request->input('passrepeat');
        
        $validateUser = Auth::attempt(['email' => $request->user()->email, 'password' => $password]);
        
        if ($validateUser != false) {
            if ($passwordNew == $passwordRepeat) {
                $user = User::where('email', $request->user()->email)->first();
                
                $user->password = bcrypt($passwordNew);
                $user->update();
                
                return response()->json([
                    'status' => 200,
                    'action' => 1,
                    'message' => 'La contraseña se ha actualizado',
                ]);
            } else {
                return response()->json([
                    'status' => 200,
                    'action' => 0,
                    'message' => 'Las contraseña nueva no coincide con la comprobación',
                ]);
            }
        } else {
            return response()->json([
                'status' => 200,
                'action' => 0,
                'message' => 'Contraseña actual inválida',
            ]);
        }
    }
    
    public function updatePerfil(Request $request)
    {
        $name = $request->input('nombre');
        $email = $request->input('correo');
        
        $images = Slim::getImages('foto');
        $nuevonombre = $request->user()->avatar;
        $emailActual = $request->user()->email;
        
        if ($emailActual != $email) {
            $search = User::select('id')->where('email', $email)->first();
            
            if ($search != false) {
                return response()->json([
                    'status' => 200,
                    'action' => 0,
                    'message' => 'El email no se puede actualizar.',
                ]);
            }
        }
        
        if ($images != false) {
            $directorio = public_path().'/photosuser/';
            
            if ( ! file_exists($directorio)) {
                mkdir($directorio);
            }
            
            if (file_exists($directorio.$nuevonombre) && $nuevonombre != null && strpos($nuevonombre, 'http') === false ) {
                unlink($directorio.$nuevonombre);
            }
            
            $sExtension = pathinfo($images[0]['output']['name'], PATHINFO_EXTENSION);
            
            $nuevonombre = 'avatar_user'.time().'.'.$sExtension;
            
            Slim::saveFile($images[0]['output']['data'], $nuevonombre, $directorio, false);
            
            $imageInter = new Image();
            $img = $imageInter->make($directorio.$nuevonombre);
            $img->resize(120, 120)->save($directorio.$nuevonombre, 90);
        }
        
        
        $user = User::where('email', $emailActual)->first();
        
        $user->email = $email;
        $user->avatar = $nuevonombre;
        $user->name = $name;
        $user->update();
        
        return response()->json([
            'status' => 200,
            'action' => 1,
            'message' => 'Se ha actualizado correctamente',
        ]);
    }
    
    public function deleteAccount(Request $request)
    {
        $validateUser = Auth::attempt(['email' => $request->user()->email, 'password' => $request->input('passdelete')]);
    
        if ($validateUser != false) {
        
            $idUser = $request->user()->id;
            $nuevonombre = $request->user()->avatar;
    
            $directorio = public_path().'/photosuser/';
    
            if ( ! file_exists($directorio)) {
                mkdir($directorio);
            }
    
            if (file_exists($directorio.$nuevonombre) && $nuevonombre != null && ! strpos($nuevonombre, 'http')) {
                unlink($directorio.$nuevonombre);
            }
        
            Game::where(function ($query) use ($idUser) {
                $query->where('user1', $idUser)
                    ->orWhere('user2', $idUser);
            })->where('status', '1')->delete();
        
            PreGame::where('user1', $idUser)->orWhere('user2', $idUser)->delete();
            HallsWait::where('user', $idUser)->delete();
            SessionesAbly::where('user_id', $idUser)->delete();
        
            DB::update('update partidas set user1 = 0 where user1 = ?', [$idUser]);
            DB::update('update partidas set user2 = 0 where user2 = ?', [$idUser]);
            DB::delete('delete from sessions where user_id = ?',[$idUser]);
        
            User::where('id', $idUser)->delete();
    
            return response()->json([
                'status' => 200,
                'action' => 1,
                'message' => 'La cuenta ha sido eliminado',
            ]);
        } else {
            return response()->json([
                'status' => 200,
                'action' => 0,
                'message' => 'Contraseña incorrecta',
            ]);
        }
    }
}
