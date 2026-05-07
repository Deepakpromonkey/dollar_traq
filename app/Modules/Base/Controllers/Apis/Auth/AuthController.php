<?php

namespace App\Modules\Base\Controllers\Apis\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Laravel\Sanctum\HasApiTokens;
class AuthController extends Controller
{
    
    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()){

            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 200);
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                
            $user = Auth::user();

            $token = $user->createToken('auth_token')->plainTextToken;

            unset($user->password);

            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully',
                'user' => $user,
                'account_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }else{

            return response()->json([
                'status' => false,
                'message' => 'The provided credentials do not match our records.',
            ], 200);
        }
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'user' => $user,
            'account_token' => $token,
        ]);
    }
}
