<?php

namespace App\Http\Controllers\API;

use App\User;
use Cassandra\Custom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Helpers\CustomResponse;

class UserController
{
    private $result = [];

    public function login(Request $request)
    {

        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $this->result["data"] = $request->all();
            $this->result["data"]["token"] = $user->createToken('MyApp')->accessToken;

            return CustomResponse::customResponse($this->result, CustomResponse::$successCode, __("api.login has been created successfully"));
        }

        return CustomResponse::customResponse($this->result, CustomResponse::$unAuthorized, __("api.invalid credentials"));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|max:30'
        ]);

        if ($validator->fails()) {

            return CustomResponse::customResponse($validator->messages(), CustomResponse::$unprocessableEntity);
        }

        try {
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);

            $user = User::create($input);

            $this->result["data"] = $user;
            $this->result["data"]["token"] = $user->createToken('MyApp')->accessToken;

            return CustomResponse::customResponse($this->result, CustomResponse::$successCode, __('api.user has been created successfully'));

        } catch (\Exception $e) {

            return CustomResponse::customResponse($this->result, CustomResponse::$errorCode, __('api.user was not created successfully'));
        }


    }

    public function logout(Request $request)
    {
        if ($request->user() != null) {
            $token = $request->user()->token();
            $token->revoke();
            return CustomResponse::customResponse($this->result, CustomResponse::$successCode, "api.user has been logged out successfully");
        }

        return CustomResponse::customResponse($this->result, CustomResponse::$notFound, "api.please login first");
    }

    public function getCurrentUser(Request $request)
    {
        $this->result['data'] = $request->user();
        return CustomResponse::customResponse($this->result, CustomResponse::$successCode);
    }
}
