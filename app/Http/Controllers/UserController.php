<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function login(UserLoginRequest $request): UserResource
    {
        $data = $request->validated();
        $user = User::where('username', $data['username'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                'errors' => ['message' => ['Username or password is incorrect.']]
            ], 401));
        }

        $user->access_token = Str::uuid()->toString();
        $user->save();

        return new UserResource($user);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    public function update(UserUpdateRequest $request,): UserResource
    {
        $data = $request->validated();
        $user = $request->user();

        if (isset($data['full_name'])) {
            $user->full_name = $data['full_name'];
        }
        if (isset($data['username'])) {
            $user->username = $data['username'];
        }
        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }


        $user->update();
        return new UserResource($user);
    }
}
