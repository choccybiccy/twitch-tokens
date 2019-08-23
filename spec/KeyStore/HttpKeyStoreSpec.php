<?php

namespace spec\Twitch\Auth\Token\KeyStore;

use Http\Client\HttpClient;
use Jose\Component\Core\JWK;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\SimpleCache\CacheInterface;
use Twitch\Auth\Token\KeyStore\HttpKeyStore;

class HttpKeyStoreSpec extends ObjectBehavior
{
    public function let(HttpClient $client, RequestFactoryInterface $requestFactory, CacheInterface $cache)
    {
        $this->beConstructedWith($client, $requestFactory, $cache);
    }

    public function it_should_fetch_from_cache(CacheInterface $cache, JWK $key)
    {
        $id = uniqid();
        $cache->get($id)->shouldBeCalled()->willReturn($key);
        $this->get($id)->shouldReturn($key);
    }

    public function it_should_request_keys(
        CacheInterface $cache,
        HttpClient $client,
        RequestFactoryInterface $requestFactory,
        RequestInterface $request,
        ResponseInterface $response,
        StreamInterface $stream
    ) {
        $id = uniqid();
        $cache->get($id)->willReturn(false);

        $stream->getContents()->willReturn(json_encode(['keys' => [
            [
                'alg' => 'RS256',
                'e' => 'AQAB',
                'kid' => '1',
                'kty' => 'RSA',
                'n' => uniqid('key'),
                'use' => 'sig',
            ]
        ]]));
        $response->getBody()->willReturn($stream);
        $response->hasHeader('cache-control')->shouldBeCalled()->willReturn(true);
        $cacheAge = 60*60*60;
        $response->getHeader('cache-control')->willReturn(sprintf('public, max-age=%d', $cacheAge));

        $requestFactory->createRequest('GET', HttpKeyStore::KEYS_URL)->willReturn($request);
        $client->sendRequest($request)->willReturn($response);

        $cache->set($id, Argument::type(JWK::class), $cacheAge)->shouldBeCalled();

        $this->get($id)->shouldReturnAnInstanceOf(JWK::class);
    }
}
