<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private function userData(User $user): array
    {
        return [
            ['name' => 'id', 'value' => $user->id],
            ['name' => 'name', 'value' => $user->name],
            ['name' => 'email', 'value' => $user->email],
            ['name' => 'type', 'value' => $user->type],
            ['name' => 'username', 'value' => $user->username],
        ];
    }

    private function userItem(User $user): array
    {
        return [
            'href' => url('/api/users/' . $user->id),
            'data' => $this->userData($user),
            'links' => [
                [
                    'rel' => 'self',
                    'href' => url('/api/users/' . $user->id),
                ]
            ]
        ];
    }

    private function collectionResponse($items, string $href, int $statusCode = 200)
    {
        return response()->json([
            'collection' => [
                'version' => '1.0',
                'href' => $href,
                'items' => $items,
                'links' => [
                    [
                        'rel' => 'self',
                        'href' => $href,
                    ]
                ]
            ]
        ], $statusCode);
    }

    private function singleItemResponse(User $user, int $statusCode = 200)
    {
        $itemHref = url('/api/users/' . $user->id);

        return response()->json([
            'collection' => [
                'version' => '1.0',
                'href' => $itemHref,
                'items' => [
                    $this->userItem($user)
                ],
                'links' => [
                    [
                        'rel' => 'self',
                        'href' => $itemHref,
                    ],
                    [
                        'rel' => 'collection',
                        'href' => url('/api/users'),
                    ]
                ]
            ]
        ], $statusCode);
    }

    private function errorResponse($message, int $statusCode)
    {
        return response()->json([
            'collection' => [
                'version' => '1.0',
                'href' => url('/api/users'),
                'error' => [
                    'title' => 'Error',
                    'message' => $message,
                    'code' => $statusCode
                ]
            ]
        ], $statusCode);
    }

    public function index()
    {
        $users = User::all();

        $items = $users->map(function ($user) {
            return $this->userItem($user);
        })->values();

        return $this->collectionResponse($items, url('/api/users'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'type' => 'required',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'type' => $request->type,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->singleItemResponse($user, 201);
    }

    public function show(User $user)
    {
        return $this->singleItemResponse($user, 200);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'type' => 'required',
            'username' => 'required|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user->name = $request->name;
        $user->type = $request->type;
        $user->username = $request->username;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return $this->singleItemResponse($user, 200);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'collection' => [
                'version' => '1.0',
                'href' => url('/api/users'),
                'message' => 'User berhasil dihapus',
                'links' => [
                    [
                        'rel' => 'collection',
                        'href' => url('/api/users'),
                    ]
                ]
            ]
        ], 200);
    }
}