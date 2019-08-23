<?php

namespace Twitch\Auth\Token;

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Jose\Component\Signature\Serializer\Serializer;
use Twitch\Auth\Token\KeyStore\HttpKeyStore;
use Twitch\Auth\Token\KeyStore\KeyStoreInterface;

/**
 * Class Verifier.
 */
class Verifier implements VerifierInterface
{
    /**
     * @var KeyStoreInterface
     */
    protected $keyStore;

    /**
     * @var JWSSerializerManager
     */
    protected $serializerManager;

    /**
     * @var JWSVerifier
     */
    protected $verifier;

    /**
     * Verifier constructor.
     *
     * @param KeyStoreInterface $keyStore
     * @param JWSSerializerManager|null $serializerManager
     * @param JWSVerifier|null $verifier
     */
    public function __construct(
        KeyStoreInterface $keyStore = null,
        JWSSerializerManager $serializerManager = null,
        JWSVerifier $verifier = null
    ) {
        $this->keyStore = $keyStore ?? new HttpKeyStore();
        $this->serializerManager = $serializerManager ?? new JWSSerializerManager([new CompactSerializer()]);
        $this->verifier = $verifier ?? new JWSVerifier(new AlgorithmManager([new RS256()]));
    }

    /**
     * @param string $token
     *
     * @return Token|null
     * @throws \Http\Client\Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function verifyToken(string $token)
    {
        $jws = $this->serializerManager->unserialize($token);
        $token = new Token(json_decode($jws->getPayload(), true));
        $sub = $token->get('sub');
        if ($sub && $key = $this->keyStore->get($sub)) {
            if ($this->verifier->verifyWithKey($jws, $key, 0)) {
                return $token;
            }
        }
        return null;
    }
}
