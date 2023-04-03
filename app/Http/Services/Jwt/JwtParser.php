<?php

namespace App\Http\Services\Jwt;

use App\Exceptions\V1\ApiException;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Lcobucci\JWT\UnencryptedToken;

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
            throw new ApiException(401, 'Unauthorized');
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
     * Undocumented function
     *
     * @return string|false
     */
    protected function getPublicKey(): string|false
    {
        return file_get_contents(config('jwt.public_key'));
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    protected function getAlgo()
    {
        return config('jwt.encrypt_algo');
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    protected function getLeeway()
    {
        return config('jwt.leeway');
    }

    /**
     * Undocumented function
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
