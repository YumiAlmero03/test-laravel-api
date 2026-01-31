<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @OA\PathItem(
     *     path="/login",
     *
     *     @OA\Post(
     *         summary="Login with email and password",
     *         description="Authenticate user and get Sanctum token",
     *         tags={"Authentication"},
     *
     *         @OA\RequestBody(
     *             required=true,
     *             description="User credentials",
     *
     *             @OA\JsonContent(
     *                 required={"email", "password"},
     *
     *                 @OA\Property(property="email", type="string", format="email", example="test@example.com"),
     *                 @OA\Property(property="password", type="string", format="password", example="password")
     *             )
     *         ),
     *
     *         @OA\Response(
     *             response=200,
     *             description="Login successful",
     *
     *             @OA\JsonContent(
     *
     *                 @OA\Property(property="token", type="string", example="1|abcdef123456")
     *             )
     *         ),
     *
     *         @OA\Response(
     *             response=422,
     *             description="Invalid credentials"
     *         )
     *     )
     * )
     */
    public function issueToken(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        return response()->json([
            'message' => 'Login successful',
            'token' => $user->createToken('api-token')->plainTextToken,
        ], 200);
    }

    /**
     * @OA\PathItem(
     *     path="/logout",
     *
     *     @OA\Post(
     *         summary="Logout user",
     *         description="Revoke Sanctum token",
     *         tags={"Authentication"},
     *         security={{"bearerAuth":{}}},
     *
     *         @OA\Response(
     *             response=200,
     *             description="Logout successful",
     *
     *             @OA\JsonContent(
     *
     *                 @OA\Property(property="message", type="string", example="Logged out successfully")
     *             )
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
