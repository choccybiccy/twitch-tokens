<?php

namespace spec\Twitch\Auth\Token;

use Jose\Component\Core\JWK;
use Jose\Component\Signature\JWS;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use PhpSpec\ObjectBehavior;
use Twitch\Auth\Token\KeyStore\KeyStoreInterface;
use Twitch\Auth\Token\Token;

class VerifierSpec extends ObjectBehavior
{
    public function let(
        KeyStoreInterface $keyStore,
        JWSSerializerManager $serializerManager,
        JWSVerifier $verifier
    ) {
        $this->beConstructedWith($keyStore, $serializerManager, $verifier);
    }

    public function it_should_verify(
        JWSSerializerManager $serializerManager,
        JWS $jws,
        JWSVerifier $verifier,
        KeyStoreInterface $keyStore,
        JWK $jwk
    ) {
        $sub = uniqid('sub');
        $clientId = uniqid('clientId');
        $token = uniqid('token');
        $jws->getPayload()->willReturn(json_encode([
            'sub' => $sub,
            'aud' => $clientId,
            'exp' => time()+86400,
            'iat' => time(),
            'iss' => 'https://id.twitch.tv/oauth2',
        ]));

        $keyStore->get($sub)->willReturn($jwk);
        $serializerManager->unserialize($token)->willReturn($jws);
        $verifier->verifyWithKey($jws, $jwk, 0)->willReturn(true);
        $token = $this->verifyToken($token);
        $token->shouldBeAnInstanceOf(Token::class);
        $token->get('sub')->shouldReturn($sub);
    }
}
