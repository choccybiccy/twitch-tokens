<?php

namespace Twitch\Auth\Token;

/**
 * Class Token.
 */
class Token
{
    /**
     * @var array
     */
    protected $payload = [];

    /**
     * Token constructor.
     *
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    public function get(string $property)
    {
        return array_key_exists($property, $this->payload) ? $this->payload[$property] : null;
    }

    /**
     * @param string $property
     *
     * @return bool
     */
    public function has(string $property): bool
    {
        return array_key_exists($property, $this->payload);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->payload;
    }
}
