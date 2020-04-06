<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Str;

class AuthController extends Controller
{
	/**
	* @method register creates new user
	*/
    public function register(Request $request)
    {
    	$this->validate($request, [
    		'email' => 'required|email|unique:users',
    		'name'  => 'required',
    		'password' => 'required|confirmed'
    	]);
    	$hashedPass = app('hash')->make($request->password);
    	$apiSecret = Str::random(80);
    	try {
    		$user = User::create([
    			'name' => $request->name,
    			'email' => $request->email,
    			'password' => $hashedPass,
    			'api_token' => $apiSecret
    		]);

    		return response()->json(['user' => $user, 'message' => 'created'], 201);
    	} catch (\Exception $e) {
    		return response()->json(['message' => 'failed, please try again.'], 409);
    	}
    }
    /**
	* @method login will return api_token if the email/password match
	* so user will make actions using this api_token in the frontend.
	*/
	public function login(Request $request)
	{
		$this->validate($request, [
			'email' => 'required',
			'password' => 'required'
		]);

		// check if there is user with this email
		$user = User::where('email', $request->email)->first();

		if (! $user) {
			return response()
				->json(['message' => 'User with this email doesn\'t exist.'], 401);
		}
		// check for the password matching
		if (! app('hash')->check($request->password, $user->password)) {
			return response()
				->json(['message' => 
					'we coudn\'t log you with given credentials, please check your password and try again.'], 
					403);
		}

		// return the user insance since every expected error, didn't occur
		return response()->json([
			'user' => $user,
			'message' => 'logged In successfully.'
		], 201);
	}
}
