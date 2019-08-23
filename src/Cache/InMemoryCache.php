<?php

namespace Twitch\Auth\Token\Cache;

use DateTime;
use Psr\SimpleCache\CacheInterface;

/**
 * Class InMemoryCache.
 */
class InMemoryCache implements CacheInterface
{
    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @param string $key
     * @param null $default
     *
     * @return mixed|void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->cache)) {
            $expires = $this->cache[$key][0];
            if (!$expires || (new DateTime()) < $expires) {
                return $this->cache[$key][1];
            }
            $this->delete($key);
        }
        return $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param null $ttl
     *
     * @return bool
     * @throws \Exception
     */
    public function set($key, $value, $ttl = null)
    {
        $expires = null;
        if (ctype_digit((string) $ttl)) {
            $ttl = new \DateInterval(sprintf('PT%dS', $ttl));
        }

        if ($ttl) {
            $expires = (new \DateTimeImmutable())->add($ttl);
        }

        $this->cache[$key] = [$expires, $value];
        return true;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete($key)
    {
        if (array_key_exists($key, $this->cache)) {
            unset($this->cache[$key]);
        }
        return true;
    }

    /**
     * @return bool
     */
    public function clear()
    {
        $this->cache = [];
        return true;
    }

    /**
     * @param iterable $keys
     * @param null $default
     *
     * @return array|iterable
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getMultiple($keys, $default = null)
    {
        $return = [];
        foreach ($keys as $key) {
            $return[$key] = $this->get($key);
        }
        return $return;
    }

    /**
     * @param iterable $values
     * @param null $ttl
     *
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     */
    public function setMultiple($values, $ttl = null)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        return true;
    }

    /**
     * @param iterable $keys
     *
     * @return bool
     */
    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }

    /**
     * @param string $key
     *
     * @return bool
     * @throws \Exception
     */
    public function has($key)
    {
        if (array_key_exists($key, $this->cache)) {
            $expires = $this->cache[$key][0];
            return !$expires || (new DateTime()) < $expires;
        }
        return false;
    }
}
