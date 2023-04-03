<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use Illuminate\Http\Request;
use App\Http\Services\Jwt\JwtGuard;
use App\Http\Services\Jwt\JwtParser;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {

        $config = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file(config('jwt.private_key')),
            InMemory::file(config('jwt.public_key')),
        );

        $this->app->instance(Configuration::class, $config);


        Auth::extend('jwt', function (Application $app, string $name, array $config) {
            // Return an instance of Illuminate\Contracts\Auth\Guard...

            return new JwtGuard(
                // @phpstan-ignore-next-line simple null expected return
                Auth::createUserProvider($config['provider']),
                $this->app->make(Request::class),
                $this->app->make(JwtParser::class)
            );
        });
    }
}
