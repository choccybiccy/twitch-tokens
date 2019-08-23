<?php

namespace Twitch\Auth\Token\KeyStore;

use Jose\Component\Core\JWK;

/**
 * Interface KeyStoreInterface
 * @package Twitch\Auth\Token\KeyStore
 */
interface KeyStoreInterface
{
    /**
     * @param string $keyId
     *
     * @return JWK
     */
    public function get(string $keyId): JWK;
}