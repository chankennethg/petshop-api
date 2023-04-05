<?php

namespace Tests\Feature;

use Str;
use Hash;
use Tests\TestCase;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Response;
use App\Exceptions\V1\ApiHandler;
use App\Http\Services\Jwt\JwtAuth;
use Database\Factories\UserFactory;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Basic admin login
     */
    public function test_admin_can_login(): void
    {
        /** @var User $user */
        $user = UserFactory::new()->create([
            'password' => Hash::make('test'),
            'is_admin' => true,
        ]);

        $this->post('/api/v1/admin/login', [
            'email' => $user->email,
            'password' => 'test',
        ])->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment([
            'success' => 1
        ])
        ->assertJsonStructure([
            'success',
            'data' => [
                'token'
            ],
            'error',
            'errors',
            'extra',
        ]);
    }

    /**
     * Login with invalid credentials
     */
    public function test_admin_login_invalid_credentials(): void
    {
        $password = Str::random(8);

        /** @var User $user */
        $user = UserFactory::new()->create([
            'password' => $password,
            'is_admin' => true,
        ]);

        $this->expectException(ApiHandler::class);

        $this->post('/api/v1/admin/login', [
            'email' => $user->email,
            'password' => 'invalid_password',
        ])->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'data' => [],
            'error' => 'Unauthorized',
            'errors' => null,
            'trace' => '*'
        ]);
    }


    /**
     * A test to check if admin can create a user
     *
     * @return void
     */
    public function test_admin_can_create(): void
    {
        /** @var User $admin */
        $admin = UserFactory::new()->create([
            'is_admin' => true,
        ]);

        $id = (string) $admin->id;
        $token = ($this->app->make(JwtAuth::class))->createToken([
            'uuid' => $admin->uuid,
            'email' => $admin->email
        ], $id);

        $password = Hash::make(Str::random(8));
        $avatar = File::all()->random()->uuid;

        $fakeEmail = fake()->email;
        $payload = [
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
            'email' => $fakeEmail,
            'password' => $password,
            'password_confirmation' => $password,
            'avatar' => $avatar,
            'address' => fake()->address,
            'phone_number' => fake()->phoneNumber,
            'is_marketing' => fake()->boolean,
        ];

        $this->actingAs($admin)
        ->post('/api/v1/admin/create', $payload, [
            'Authorization' => "Bearer {$token}"
        ])
        ->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'data' => [
                'uuid',
                'first_name',
                'last_name',
                'email',
                'address',
                'phone_number',
                'updated_at',
                'created_at',
                'token',
            ],
            'error',
            'errors',
            'extra'
        ])
        ->assertJsonFragment([
            'success' => 1,
            'email' => $fakeEmail
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $fakeEmail,
            'is_admin' => true
        ]);

    }

    /**
     * Test endpoint with invalid data
     * Missing Required Fields
     * Missing Token
     *
     * @return void
     */
    public function test_admin_can_create_with_invalid_data(): void
    {
        /** @var User $admin */
        $admin = UserFactory::new()->create([
            'is_admin' => true,
        ]);

        $id = (string) $admin->id;
        $token = ($this->app->make(JwtAuth::class))->createToken([
            'uuid' => $admin->uuid,
            'email' => $admin->email
        ], $id);

        $this->expectException(ValidationException::class);
        // Missing Payload
        $this->actingAs($admin)
        ->post('/api/v1/admin/create', [], [
            'Authorization' => "Bearer {$token}"
        ])
        ->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors' => [
                'first_name',
                'last_name',
                'email',
                'password',
                'password_confirmation',
                'address',
                'phone_number',
                ''
            ],
            'trace'
        ])
        ->assertJsonFragment([
            'success' => 0,
            'error' => 'Validation Error'
        ]);


        // No token
        $this->actingAs($admin)
        ->post('/api/v1/admin/create', [])
        ->assertStatus(401)
        ->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'trace'
        ])
        ->assertJsonFragment([
            'success' => 0,
            'error' => 'Unauthorized'
        ]);
    }

    /**
     * Test if admin can delete a user
     *
     * @return void
     */
    public function test_admin_can_delete_user(): void
    {
        /** @var User $user */
        $user = UserFactory::new()->create([
            'is_admin' => false,
        ]);

        /** @var User $admin */
        $admin = UserFactory::new()->create([
            'is_admin' => true,
        ]);

        $tokenHeader = $this->createTokenHeader($admin->email, $admin->id, $admin->uuid);

        $this->delete("/api/v1/admin/user-delete/{$user->uuid}", [], $tokenHeader)
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'extra',
        ])
        ->assertJsonFragment([
            'success' => 1,
        ]);
    }

    /**
     * Test if admin cannot delete a missing user
     *
     * @return void
     */
    public function test_admin_cant_delete_missing_user(): void
    {
        /** @var User $admin */
        $admin = UserFactory::new()->create([
            'is_admin' => true,
        ]);

        $tokenHeader = $this->createTokenHeader($admin->email, $admin->id, $admin->uuid);

        $this->expectException(ApiHandler::class);
        $this->delete("/api/v1/admin/user-delete/fake-uuid", [], $tokenHeader)
        ->assertStatus(404)
        ->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'extra',
        ])
        ->assertJsonFragment([
            'success' => 0,
        ]);
    }

    /**
     * Test if a normal user can delete a user
     *
     * @return void
     */
    public function test_normal_user_cannot_delete_user(): void
    {
        /** @var User $user */
        $user = UserFactory::new()->create([
            'is_admin' => false,
        ]);

        /** @var User $user2 */
        $user2 = UserFactory::new()->create([
            'is_admin' => false,
        ]);

        $tokenHeader = $this->createTokenHeader($user->email, $user->id, $user->uuid);

        $this->expectException(UnauthorizedHttpException::class);
        $this->delete("/api/v1/admin/user-delete/{$user2->uuid}", [], $tokenHeader)
        ->assertStatus(401)
        ->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'extra',
        ])
        ->assertJsonFragment([
            'success' => 0,
        ]);
    }

    /**
     * Test if a admin can edit a user
     *
     * @return void
     */
    public function test_admin_can_edit_user(): void
    {
        /** @var User $user */
        $user = UserFactory::new()->create([
            'is_admin' => false,
        ]);

        /** @var User $admin */
        $admin = UserFactory::new()->create([
            'is_admin' => true,
        ]);

        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        $payload = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => fake()->email(),
            'password' => '123456789',
            'password_confirmation' => '123456789',
            'address' => fake()->address(),
            'phone_number' => fake()->phoneNumber(),
            'avatar' => File::all()->random()->uuid,
        ];

        $tokenHeader = $this->createTokenHeader($admin->email, $admin->id, $admin->uuid);

        $this->put("/api/v1/admin/user-edit/{$user->uuid}", $payload, $tokenHeader)
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'extra',
        ])
        ->assertJsonFragment([
            'success' => 1,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ]);
    }

    /**
     * Test if a admin can edit admin
     *
     * @return void
     */
    public function test_admin_cannot_edit_admin(): void
    {
        /** @var User $admin */
        $admin = UserFactory::new()->create([
            'is_admin' => true,
        ]);

        /** @var User $admin2 */
        $admin2 = UserFactory::new()->create([
            'is_admin' => true,
        ]);

        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        $payload = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => fake()->email(),
            'password' => '123456789',
            'password_confirmation' => '123456789',
            'address' => fake()->address(),
            'phone_number' => fake()->phoneNumber(),
            'avatar' => File::all()->random()->uuid,
        ];

        $tokenHeader = $this->createTokenHeader($admin->email, $admin->id, $admin->uuid);

        $this->expectException(ApiHandler::class);
        $this->put("/api/v1/admin/user-edit/{$admin2->uuid}", $payload, $tokenHeader)
        ->assertStatus(404)
        ->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'extra',
        ])
        ->assertJsonFragment([
            'success' => 0,
        ]);
    }
}
