<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
class LoginController extends Controller
{
    public function login(Request $request)
    {
        $input = $request->all();
        $validation = Validator::make($input,[
            'email' => 'required|string|email|exists:admins',
            'password' => 'required|string|min:6|max:255',
        ]);
        if ($validation->fails())
        return $this->error($validation->errors()->first());
        if (Auth::guard('admin')->attempt(['email'=>$input['email'],'password'=>$input['password']])){
            $user = Auth::guard('admin')->user();
            $token = $user->createToken('myApp', ['admin'])->plainTextToken;
            return $this->success($token);
        }

        return $this->error();
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();
        return response()->json(["data"=>$user]);
    }
}
