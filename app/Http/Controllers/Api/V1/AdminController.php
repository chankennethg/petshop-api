<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\V1\ApiException;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Admin\AdminCreateUserRequest;
use App\Http\Requests\V1\Admin\AdminUserListingRequest;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Services\Jwt\JwtAuth;
use App\Traits\ApiTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Response;

class AdminController extends Controller
{
    use ApiTransformer;

    public function __construct()
    {
        $this->middleware('auth:api')->except('login');
    }

    /**
     * Lists non-admin users
     *
     * @param AdminUserListingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(AdminUserListingRequest $request)
    {
        $filters = $request->safe()->only(['first_name']);

        $users = User::NonAdmin()
            ->filter($filters)
            ->sort($request)
            ->paginate($request->get('limit', 10));

        return Response::json($users);
    }

    /**
     * User Login
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request, JwtAuth $jwtAuth): JsonResponse
    {
        $credentials = $request->safe()->all();
        $credentials['is_admin'] = true;

        // Check if credentials match
        if (!Auth::attempt($credentials)) {
            throw new ApiException(422, 'Unauthorized');
        }

        /** @var User */
        $user = Auth::user();
        $user->last_login_at = now();
        $user->save();

        // Create token
        $id = (string) $user->id;
        $token = $jwtAuth->createToken([
            'uuid' => $user->uuid,
            'email' => $user->email
        ], $id);

        return $this->toResponse(200, 1, [
            'token' => $token
        ]);
    }

    /**
     * Create admin user
     *
     * @param AdminCreateUserRequest $request
     * @param JwtAuth $jwtAuth
     * @return JsonResponse
     */
    public function createAdmin(AdminCreateUserRequest $request, JwtAuth $jwtAuth): JsonResponse
    {
        $data = $request->safe()->all();

        /** @var User */
        $user = new User();
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $request['email'];
        $user->password = Hash::make($request['password']);
        $user->address = $request['address'];
        $user->phone_number = $request['phone_number'];
        $user->avatar = $request['avatar'];
        $user->is_marketing = $request['marketing'] ?? false;
        $user->is_admin = true;
        $user->save();

        $id = (string) $user->id;
        $token = $jwtAuth->createToken([
            'uuid' => $user->uuid,
            'email' => $user->email
        ], $id);

        $responseBody = $user->only([
            'uuid', 'first_name', 'last_name', 'email', 'address', 'phone_number', 'updated_at', 'created_at'
        ]);
        $responseBody['token'] = $token;

        return $this->toResponse(200, 0, $responseBody);
    }
}
