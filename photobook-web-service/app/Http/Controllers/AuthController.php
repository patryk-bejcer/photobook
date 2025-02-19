<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller {
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware( 'auth:api', [ 'except' => [ 'login', 'register' ] ] );
    }

    /**
     * @OA\Post(
     * path="/api/auth/login",
     * summary="Sign in",
     * description="Login by email, password",
     * operationId="authLogin",
     * tags={"Auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="test123"),
     *    ),
     * ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *     @OA\JsonContent(
     *        @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKOiJIUdpbiIsImlhdCI6MTYwMTA0NoEQ"),
     *        @OA\Property(property="token_type", type="string", example="bearer"),
     *        @OA\Property(property="expires_in", type="integer", example="3600"),
     *        @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *     )
     *       ),
     * @OA\Response(
     *    response=401,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login( Request $request ) {
        $validator = Validator::make( $request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ] );

        if ( $validator->fails() ) {
            return response()->json( $validator->errors(), 422 );
        }

        if ( ! $token = auth()->attempt( $validator->validated() ) ) {
            return response()->json( [ 'error' => 'Sorry, wrong email address or password. Please try again' ], 401 );
        }

        return $this->createNewToken( $token );
    }

    /**
     *
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function createNewToken( $token ) {
        return response()->json( [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60,
            'user'         => auth()->user()
        ] );
    }

    /**
     * @OA\Post(
     * path="/api/auth/register",
     * summary="Sign up",
     * description="Register by name, email, password",
     * operationId="authRegister",
     * tags={"Auth"},
     *
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass register credentials",
     *    @OA\JsonContent(
     *       required={"name", "email","password", "password_confirmation"},
     *       @OA\Property(property="name", type="string", example="John Smith"),
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="test123"),
     *       @OA\Property(property="password_confirmation", type="string", format="password", example="test123"),
     *    ),
     * ),
     * @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *     @OA\JsonContent(
     *        @OA\Property(property="message", type="string", example="User successfully registered"),
     *        @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *     )
     *       ),
     * @OA\Response(
     *    response=401,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="The name field is required.")
     *        )
     *     )
     * )
     *
     * Register a User.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function register( Request $request ) {
        $validator = Validator::make( $request->all(), [
            'name'     => 'required|string|between:2,100',
            'email'    => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ] );

        if ( $validator->fails() ) {
            return response()->json( $validator->errors()->toJson(), 400 );
        }

        $user = User::create( array_merge(
            $validator->validated(),
            [ 'password' => bcrypt( $request->password ) ]
        ) );

        return response()->json( [
            'message' => 'User successfully registered',
            'user'    => $user
        ], 201 );
    }

    /**
     *
     * @OA\Post(
     * path="/api/auth/logout",
     * summary="Sign out",
     * description="Logout",
     * operationId="authLogout",
     * tags={"Auth"},
     * @OA\Response(
     *    response=200,
     *    description="Successful operation",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="User successfully signed out"),
     *    )
     * ),
     *  security={{ "apiAuth": {} }},
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated.")
     *        )
     *     )
     * )
     *
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout() {
        auth()->logout();

        return response()->json( [ 'message' => 'User successfully signed out' ] );
    }

    /**
     *
     * @OA\Post(
     * path="/api/auth/refresh",
     * summary="Refresh token",
     * description="Refresh JWT Token",
     * operationId="authRefresh",
     * tags={"Auth"},
     * @OA\Response(
     *    response=200,
     *    description="Successful operation",
     *    @OA\JsonContent(
     *        @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKOiJIUdpbiIsImlhdCI6MTYwMTA0NoEQ"),
     *        @OA\Property(property="token_type", type="string", example="bearer"),
     *        @OA\Property(property="expires_in", type="integer", example="3600"),
     *        @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *    )
     * ),
     * security={{ "apiAuth": {} }},
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated.")
     *        )
     *     )
     * )
     *
     * Log the user out (Invalidate the token).
     *
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh() {
        return $this->createNewToken( auth()->refresh() );
    }

    /**
     *
     * @OA\Get(
     * path="/api/auth/user-profile",
     * summary="Get user profile",
     * description="Get user profile details",
     * operationId="authUserProfile",
     * tags={"Auth"},
     * @OA\Response(
     *    response=200,
     *    description="Successful operation",
     *    @OA\JsonContent(
     *        @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *    )
     * ),
     * security={{ "apiAuth": {} }},
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated.")
     *        )
     *     )
     *
     * )
     *
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function userProfile() {
        return response()->json( auth()->user() );
    }
}
