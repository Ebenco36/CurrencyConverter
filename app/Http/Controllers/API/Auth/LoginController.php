<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Auth;
use App\User;
use Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
class LoginController extends Controller
{
    public function login(Request $request)
    {
		$validator = Validator::make($request->all(),[
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);
		if ($validator->fails()) {
            $exp = response()->json(['error'=>$validator->errors()]);
            //return $exp;
            return Redirect::back()->withErrors(['message'=>'All Fields are Mandatory']);
        }else{
			$credentials = $request->only('email', 'password');

            try {
                if (! $token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'invalid_credentials'], 400);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'could_not_create_token'], 500);
            }
			
            return response()->json(compact('token'));
		}
	}
	
	

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
		try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
			return response()->json(['token_expired'], 403);
		} catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
			return response()->json(['token_invalid'], 403);
		} catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
			return response()->json(['token_absent'], 422);
		}
		catch (Exception $e) {
			return response()->json(['token_absent'], $e);
		}
		return response()->json(compact('user'));
    }
}
