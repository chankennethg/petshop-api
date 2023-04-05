<?php

namespace App\Http\Services\Jwt;

use Lcobucci\JWT\Configuration;
use App\Exceptions\V1\ApiHandler;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;

class JwtParser
{
    /**
     * @var Configuration
     */
    private $config;

    /**
     * Class constructor
     *
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * @var UnencryptedToken
     */
    protected $token;

    /**
     * Undocumented function
     *
     * @param non-empty-string $token
     * @return self
     */
    public function parse(string $token)
    {
        try {
            // @phpstan-ignore-next-line
            $this->token = $this->config->parser()->parse($token);
        } catch (CannotDecodeContent | InvalidTokenStructure | UnsupportedHeaderFound $e) {
            throw new ApiHandler(401, 'Unauthorized');
        }
        return $this;
    }

    /**
     * Get iss
     *
     * @return mixed
     */
    public function getIssuedBy()
    {
        return $this->getClaim('iss');
    }

    /**
     * Get iat
     *
     * @return mixed
     */
    public function getIssuedAt()
    {
        return $this->getClaim('iat');
    }

    /**
     * Get sub
     *
     * @return mixed
     */
    public function getRelatedTo()
    {
        return $this->getClaim('sub');
    }

    /**
     * Get aud
     *
     * @return mixed
     */
    public function getAudience()
    {
        return $this->getClaim('aud');
    }

    /**
     * get exp
     *
     * @return mixed
     */
    public function getExpiresAt()
    {
        return $this->getClaim('exp');
    }

    /**
     * get jti
     *
     * @return mixed
     */
    public function getIdentifiedBy()
    {
        return $this->getClaim('jti');
    }

    /**
     * get nbf
     *
     * @return mixed
     */
    public function getCanOnlyBeUsedAfter()
    {
        return $this->getClaim('nbf');
    }

    /**
     * Get Claim index
     *
     * @param non-empty-string $name
     * @return mixed
     */
    protected function getClaim(string $name)
    {
        return $this->token->claims()->get($name) ?? null;
    }

    /**
     * Get Public Key
     *
     * @return string|false
     */
    protected function getPublicKey(): string|false
    {
        return file_get_contents(config('jwt.public_key'));
    }

    /**
     * Get Algo
     *
     * @return string
     */
    protected function getAlgo(): string
    {
        return config('jwt.encrypt_algo');
    }

    /**
     * Get Leeway
     *
     * @return mixed
     */
    protected function getLeeway()
    {
        return config('jwt.leeway');
    }

    /**
     * Get TTL
     *
     * @return mixed
     */
    protected function getTtl()
    {
        return config('jwt.ttl');
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    protected function supportedAlgos()
    {
        return config('jwt.supported_algos');
    }
}
