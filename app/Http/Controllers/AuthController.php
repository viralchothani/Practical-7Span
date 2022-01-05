<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            $response['message'] =  'Validation error';
            $response['error'] =  $validator->errors();
            return response()->json(['data' => $response], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'status' => $request->status
        ]);
       
        $token = $user->createToken('AccessToken')->accessToken;

        $response['message'] =  'Register Successfully';
        $response['token'] =  $token; 

        return response()->json(['data' => $response], 200);
    }
 
    /**
     * Login
     */
    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
 
        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('AccessToken')->accessToken;
            $user = auth()->user();

            $response['message'] =  'Login Successfully';
            $response['token'] =  $token; 
            $response['name'] =  $user->name;

            return response()->json(['data' => $response], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }
 
    /**
     * Uodate
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            $response['message'] =  'Validation error';
            $response['error'] =  $validator->errors();
            return response()->json(['data' => $response], 422);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 400);
        }
        
        $userParams = array();
        $userParams['name'] = request('name');
        $userParams['password'] = bcrypt($request->password);

        $updated = $user->fill($userParams)->save();
        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'User updated'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User can not be updated'
            ], 500);
        }
    }
}
