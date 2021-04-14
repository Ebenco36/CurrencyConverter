<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Auth;
Use App\User;
use Validator;
use Hash;
use JWTAuth;    
use Tymon\JWTAuth\Exceptions\JWTException;
class RegisterController extends Controller
{
    /**
     * @var UserRepository
    */
    protected $repository;
    protected $model;
    public function __construct(){
        $user = new User();
        $this->model = new UserRepository($user);
    }
	
	
	/**
     * @var Register New User
    */
	
	public function store(Request $request)
	{
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|unique:users,email', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);
		if ($validator->fails()) { 
			return response()->json(['error'=>$validator->errors()], 401);            
		}
        try {
            $request->merge(['password' => Hash::make($request->password)]);
            $user   = $this->model->create($request->only($this->model->getModel()->fillable));
			$token = JWTAuth::fromUser($user);

            return response()->json(compact('user','token'),201);
			

        } catch (ValidatorException $e) {
			return response(json_encode([
				'error'      =>true,
                'message'    =>$e->getMessage()
			]), 422);

        }
    }
}
