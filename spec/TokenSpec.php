<?php

namespace spec\Twitch\Auth\Token;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Twitch\Auth\Token\Token;

class TokenSpec extends ObjectBehavior
{
    protected $payload;

    public function let()
    {
        $clientId = uniqid('clientId');
        $payload = [
            'sub' => uniqid('sub'),
            'aud' => $clientId,
            'exp' => time()+86400,
            'iat' => time(),
            'iss' => 'https://id.twitch.tv/oauth2',
        ];
        $this->payload = $payload;
        $this->beConstructedWith($payload);
    }

    public function it_should_get_properties()
    {
        foreach(array_keys($this->payload) as $property) {
            $this->get($property)->shouldReturn($this->payload[$property]);
        }
    }

    public function it_should_return_properties_as_array()
    {
        $this->toArray()->shouldBeArray();
        foreach(array_keys($this->payload) as $property) {
            $this->toArray()->shouldHaveKeyWithValue($property, $this->payload[$property]);
        }
    }
}
