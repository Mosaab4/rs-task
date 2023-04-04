<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $user = User::where('email', $request['email'])->first();

        if (!$user) {
            return $this->respondUnauthorized('Password mismatch');
        }

        if (!Hash::check($request['password'], $user->password)) {
            return $this->respondUnauthorized('Password mismatch');
        }

        $token = $user->createToken('Password Grant Token');
        $user->token = $token->plainTextToken;

        return $this->respondWithSuccess(new UserResource($user));
    }
}
