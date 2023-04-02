<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Admin\AdminUserListingRequest;
use App\Models\User;
use Response;

class AdminController extends Controller
{
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
     * Undocumented function
     *
     * @return void
     */
    public function login(): void
    {
        // @todo Something
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function createAdmin(): void
    {
        // @todo Something
    }
}
