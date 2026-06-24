<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class ApiAuthController extends Controller
{
    #[OA\Post(
        path: '/auth/login',
        summary: 'Login user',
        description: 'Login menggunakan email & password. Mengembalikan Bearer token yang berlaku 30 hari.',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'petugas@toya.desa.id'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'rahasia123'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login berhasil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Login berhasil.'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'token', type: 'string', example: '1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'),
                                new OA\Property(
                                    property: 'user',
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', example: 3),
                                        new OA\Property(property: 'name', type: 'string', example: 'Budi Santoso'),
                                        new OA\Property(property: 'email', type: 'string', example: 'petugas@toya.desa.id'),
                                        new OA\Property(property: 'phone', type: 'string', example: '081234567890'),
                                        new OA\Property(property: 'role', type: 'string', example: 'petugas'),
                                    ],
                                    type: 'object'
                                ),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'meta', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Akun nonaktif',
                content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Email atau password tidak sesuai',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
            ),
        ]
    )]
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

    #[OA\Get(
        path: '/auth/me',
        summary: 'Profil user yang sedang login',
        description: 'Mengembalikan data user. Jika role pelanggan, akan disertakan field `customer`.',
        security: [['sanctum' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'OK'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 10),
                                new OA\Property(property: 'name', type: 'string', example: 'Wayan Karya'),
                                new OA\Property(property: 'email', type: 'string', example: 'wayan@gmail.com'),
                                new OA\Property(property: 'phone', type: 'string', example: '082233445566'),
                                new OA\Property(property: 'role', type: 'string', example: 'pelanggan'),
                                new OA\Property(
                                    property: 'customer',
                                    nullable: true,
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', example: 5),
                                        new OA\Property(property: 'customer_number', type: 'string', example: 'PLG-0005'),
                                        new OA\Property(property: 'name', type: 'string', example: 'Wayan Karya'),
                                        new OA\Property(property: 'address', type: 'string', example: 'Banjar Kaja No. 12'),
                                        new OA\Property(property: 'zone', type: 'string', example: 'Zona A'),
                                        new OA\Property(property: 'tariff', type: 'string', example: 'Tarif Rumah Tangga'),
                                    ],
                                    type: 'object'
                                ),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'meta', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
        ]
    )]
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

    #[OA\Post(
        path: '/auth/logout',
        summary: 'Logout',
        description: 'Menghapus token akses yang sedang digunakan.',
        security: [['sanctum' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logout berhasil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Logout berhasil.'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                        new OA\Property(property: 'meta', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
        ]
    )]
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
