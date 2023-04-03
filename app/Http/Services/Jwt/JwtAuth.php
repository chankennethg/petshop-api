<?php

namespace App\Http\Services\Jwt;

use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class JwtAuth
{

    /**
     * @var Configuration
     */
    private $config;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * Createa a JWT Token
     *
     * @param array<non-empty-string,mixed> $claims
     * @param non-empty-string $userId
     * @return string
     */
    public function createToken(array $claims, string $userId)
    {
        $signer = new Sha256;
        $now = (new DateTimeImmutable());
        $ttl = config('jwt.ttl');

        $builder = $this->config
            // boot builder
            ->builder()
            // Configures the issuer (iss claim)
            ->issuedBy(config('app.name'))
            // Configures the audience (aud claim)
            ->permittedFor(config('app.url'))
            // Configures the time that the token was issue (iat claim)
            ->issuedAt($now)
            // Configures the expiration time of the token (exp claim)
            ->expiresAt($now->modify("+{$ttl} seconds"))
            ->relatedTo($userId);

        // Add claims
        foreach ($claims as $key => $value) {
            $builder = $builder->withClaim($key, $value);
        }

        return $builder->getToken($this->config->signer(), $this->config->signingKey())->toString();
    }
}
