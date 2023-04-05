<?php

namespace App\Http\Services\Jwt;

use Illuminate\Http\Request;
use App\Exceptions\V1\ApiHandler;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;

class JwtGuard implements Guard
{
    use GuardHelpers;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var JwtParser
     */
    private $parser;

    /**
     * Class constructor
     *
     * @param UserProvider $provider
     * @param Request $request
     * @param JwtParser $parser
     */
    public function __construct(UserProvider $provider, Request $request, JwtParser $parser)
    {
        $this->provider = $provider;
        $this->request = $request;
        $this->parser = $parser;
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $this->user = $this->authenticateByToken();

        if (!$this->user) {
            throw new ApiHandler(401, 'Unauthorized');
        }

        return $this->user;
    }

    /**
     * Validate a user's credentials.
     *
     * @param array<string,mixed> $credentials
     * @return bool
     */
    public function validate(array $credentials = []): bool
    {
        if (!$credentials) {
            return false;
        }

        if ($this->provider->retrieveByCredentials($credentials)) {
            return true;
        }

        return false;
    }

    /**
     * Undocumented function
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected function authenticateByToken()
    {
        if (!empty($this->user)) {
            return $this->user;
        }

        $token = $this->getBearerToken();

        if (empty($token)) {
            return null;
        }

        try {
            $decoded = $this->parser->parse($token);

            if (now()->greaterThanOrEqualTo($decoded->getExpiresAt())) {
                return null;
            }

            $user = $this->provider->retrieveById($decoded->getRelatedTo());
        } catch (\Exception $exception) {
            $user = null;
        }

        return $user;
    }

    /**
     * Get Bearer token from header
     *
     * @return string|null
     */
    protected function getBearerToken(): ?string
    {
        return $this->request->bearerToken();
    }

    /**
     * Attempt auth
     *
     * @param array<string,mixed> $credentials
     * @return bool
     */
    public function attempt(array $credentials = []): bool
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if ($this->hasValidCredentials($user, $credentials)) {
            $this->user = $user;
            return true;
        }

        return false;
    }

    /**
     * Check of credentials is valid
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     * @param array<string,mixed> $credentials
     * @return bool
     */
    protected function hasValidCredentials($user, $credentials): bool
    {
        return $user !== null && $this->provider->validateCredentials($user, $credentials);
    }
}
