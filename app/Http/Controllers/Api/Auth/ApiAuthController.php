<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak sesuai.',
            ]);
        }

        $user  = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak aktif. Hubungi administrator.',
                'data'    => null,
                'meta'    => null,
            ], 403);
        }

        $token = $user->createToken('mobile', ['*'], now()->addDays(30))->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'token' => $token,
                'user'  => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role'  => $user->role,
                ],
            ],
            'meta' => null,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role'  => $user->role,
        ];

        // Sertakan data pelanggan jika role pelanggan
        if ($user->role === 'pelanggan') {
            $customer = \App\Models\Customer::with(['zone', 'tariffRate'])
                ->where('user_id', $user->id)
                ->first();

            $data['customer'] = $customer ? [
                'id'              => $customer->id,
                'customer_number' => $customer->customer_number,
                'name'            => $customer->name,
                'address'         => $customer->address,
                'zone'            => $customer->zone?->name,
                'tariff'          => $customer->tariffRate?->name,
            ] : null;
        }

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $data,
            'meta'    => null,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
            'data'    => null,
            'meta'    => null,
        ]);
    }
}
