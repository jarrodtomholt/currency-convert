<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request): JsonResource
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password_confirmation,
        ]);

        $user->token = $user->createToken($request->ip())->plainTextToken;

        return AuthResource::make($user);
    }
}
