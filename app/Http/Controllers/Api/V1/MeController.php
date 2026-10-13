<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MeController extends Controller
{
    public function show(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diambil',
            'data' => new UserResource($request->user()),
        ]);
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:30'],
                'password' => ['nullable', 'string', 'min:8'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui profil',
                'data' => $e->errors(),
            ], 422);
        }

        $user = $request->user();
        $user->name = $validated['name'];

        if (array_key_exists('phone', $validated)) {
            $user->phone = $validated['phone'];
        }

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => new UserResource($user),
        ]);
    }
}
