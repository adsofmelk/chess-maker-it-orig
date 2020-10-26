<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Socialite;
use App\User;

class SocialAuthController extends Controller
{

	protected $redirectLogin = 'home';

    public function getFacebook()
    {
    	return Socialite::driver('facebook')->redirect();
    }

    public function cbFacebook()
    {
    	return $this->registerUserWithSocial('facebook');
    }

    public function getGoogle()
    {
    	return Socialite::driver('google')->redirect();
    }

    public function cbGoogle()
    {
    	return $this->registerUserWithSocial('google');
    }

    protected function registerUserWithSocial($type_red)
    {
    	$aUserSocial = Socialite::driver($type_red)->user();
    	$aUser = User::where('id_profile', $aUserSocial->id)->orWhere('email',$aUserSocial->email)->first();

    	$bLogin = false;

    	if ($aUser == null) {
    		$aUser = User::create([
    			'name'       => $aUserSocial->name,
    			'email'      => $aUserSocial->email,
    			'avatar'     => $aUserSocial->avatar,
    			'password'   => Str::random(16),
    			'type_auth'  => $type_red,
    			'id_profile' => $aUserSocial->id,
    		]);

    		$bLogin = true;
    	} else if ($aUser->type_auth == 'local') {
			$aUser = User::create([
    			'name'       => $aUserSocial->name,
    			'email'      => $aUserSocial->email,
    			'avatar'     => $aUserSocial->avatar,
    			'password'   => Str::random(16),
    			'type_auth'  => 'local - '.$type_red,
    			'id_profile' => $aUserSocial->id,
    		]);

    		$bLogin = true;
		} else {
    	    if(!strpos($aUser->avatar, 'http')===false){
                $aUser->avatar = $aUserSocial->avatar;
            }
			
			$aUser->save();

			$bLogin = true;
		}

		if ($bLogin) {
			auth()->login($aUser);
    		return redirect($this->redirectLogin);
		} else {
			return response()->json([
	    		'status' => 401,
	    		'message' => 'No se ha podido iniciar sesión con redes sociales',
	    	]);
		}
    }
}
