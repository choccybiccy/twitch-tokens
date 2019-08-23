<?php

namespace Twitch\Auth\Token\KeyStore;

use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use Jose\Component\Core\JWK;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Twitch\Auth\Token\Cache\InMemoryCache;

/**
 * Class HttpKeyStore.
 */
class HttpKeyStore implements KeyStoreInterface
{
    const KEYS_URL = 'https://id.twitch.tv/oauth2/keys';

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var RequestFactoryInterface
     */
    protected $requestFactory;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * HttpKeyStore constructor.
     *
     * @param HttpClient $httpClient
     * @param RequestFactoryInterface $requestFactory
     * @param CacheInterface $cache
     */
    public function __construct(
        HttpClient $httpClient = null,
        RequestFactoryInterface $requestFactory = null,
        CacheInterface $cache = null
    ) {
        $this->httpClient = $httpClient ?? HttpClientDiscovery::find();
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
        $this->cache = $cache ?? new InMemoryCache();
    }

    /**
     * @param string $keyId
     *
     * @return JWK
     * @throws \Exception
     * @throws InvalidArgumentException
     * @throws \Http\Client\Exception
     */
    public function get(string $keyId): JWK
    {
        if ($key = $this->cache->get($keyId)) {
            return $key;
        }

        $response = $this->httpClient->sendRequest(
            $this->requestFactory->createRequest('GET', self::KEYS_URL)
        );

        $ttl = null;
        if ($response->hasHeader('cache-control')
            && preg_match('/max-age=(\d+)/i', $response->getHeader('cache-control'), $matches)
        ) {
            $ttl = $matches[1];
        }

        $keys = json_decode($response->getBody()->getContents(), true);
        if (!$keys) {
            throw new \Exception('Unexpected response from Twitch keys endpoint');
        }

        $key = new JWK($keys['keys'][0]);

        $this->cache->set($keyId, $key, $ttl);

        return $key;
    }
}
