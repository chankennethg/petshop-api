<?php

namespace App\Http\Controllers\Api\V1;

use Response;
use App\Models\User;
use App\Exceptions\V1\ApiHandler;
use Illuminate\Http\JsonResponse;
use App\Http\Services\Jwt\JwtAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Resources\V1\BaseResource;
use App\Http\Resources\V1\Admin\AdminEditResource;
use App\Http\Resources\V1\Admin\AdminLoginResource;
use App\Http\Requests\V1\Admin\AdminEditUserRequest;
use App\Http\Resources\V1\Admin\AdminCreateResource;
use App\Http\Requests\V1\Admin\AdminCreateUserRequest;
use App\Http\Requests\V1\Admin\AdminUserListingRequest;

class AdminController extends Controller
{
    /**
     * Class Constructor
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except('login');
    }

    /**
     * Lists non-admin users
     *
     * @param AdminUserListingRequest $request
     * @return JsonResponse
     */
    public function list(AdminUserListingRequest $request): JsonResponse
    {
        $filters = $request->safe()->all();
        $sortBy = $filters['sortBy'] ?? 'created_at';
        $direction = $filters['desc'] ?? 'true';

        $users = User::NonAdmin()
            ->filter($filters)
            ->sort($sortBy, $direction)
            ->paginate($request->get('limit', 10));

        return Response::json($users);
    }

    /**
     * User Login
     *
     * @param LoginRequest $request
     * @return AdminLoginResource
     */
    public function login(LoginRequest $request, JwtAuth $jwtAuth): AdminLoginResource
    {
        $credentials = $request->safe()->all();
        $credentials['is_admin'] = true;

        // Check if credentials match
        if (!Auth::guard('api')->attempt($credentials)) {
            throw new ApiHandler(422, 'Unauthorized');
        }

        /** @var User $user*/
        $user = Auth::guard('api')->user();

        $user->last_login_at = now();
        $user->save();

        // Create token
        $id = (string) $user->id;
        $token = $jwtAuth->createToken([
            'uuid' => $user->uuid,
            'email' => $user->email,
        ], $id);

        return new AdminLoginResource($user, $token);
    }

    /**
     * Create admin user
     *
     * @param AdminCreateUserRequest $request
     * @param JwtAuth $jwtAuth
     * @return AdminCreateResource
     */
    public function createAdmin(
        AdminCreateUserRequest $request,
        JwtAuth $jwtAuth
    ): AdminCreateResource {
        $data = $request->safe()->all();

        /** @var User $user*/
        $user = new User();
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $request['email'];
        $user->password = Hash::make($request['password']);
        $user->address = $request['address'];
        $user->phone_number = $request['phone_number'];
        $user->avatar = $request['avatar'];
        $user->is_marketing = $request['is_marketing'];
        $user->is_admin = true;
        $user->save();

        $token = $jwtAuth->createToken([
            'uuid' => $user->uuid,
            'email' => $user->email,
        ], (string) $user->id);

        return new AdminCreateResource($user, $token);
    }

    /**
     * Delete a user
     *
     * @param string $uuid
     * @throws ApiHandler
     * @return BaseResource
     */
    public function deleteUser(string $uuid): BaseResource
    {
        $result = User::uuid($uuid)
            ->where('is_admin', false)
            ->delete();

        if (!$result) {
            throw new ApiHandler(404, 'User not found');
        }

        return new BaseResource($result);
    }

    /**
     * Update a user
     *
     * @param AdminEditUserRequest $request
     * @param string $uuid
     * @throws ApiHandler
     * @return AdminEditResource
     */
    public function editUser(AdminEditUserRequest $request, string $uuid): AdminEditResource
    {
        $payload = $request->safe()->all();
        $result = User::uuid($uuid)
            ->where('is_admin', false)
            ->update($payload);

        if (!$result) {
            throw new ApiHandler(404, 'User not found');
        }

        return new AdminEditResource(User::uuid($uuid)->first());
    }
}
