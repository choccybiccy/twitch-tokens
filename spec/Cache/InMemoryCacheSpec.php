<?php

namespace spec\Twitch\Auth\Token\Cache;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Twitch\Auth\Token\Cache\InMemoryCache;

class InMemoryCacheSpec extends ObjectBehavior
{
    public function it_should_get_and_set()
    {
        $data = uniqid();
        $this->set('key', $data, 3600);
        $this->get('key')->shouldReturn($data);

        $this->set('expired', $data, 1);
        sleep(2);
        $this->get('expired', 'default')->shouldReturn('default');
    }

    public function it_should_get_and_set_multiple()
    {
        $data = [
            'key1' => uniqid('key1'),
            'key2' => uniqid('key2'),
            'key3' => uniqid('key3'),
        ];
        $this->setMultiple($data, 3600);
        $this->getMultiple(array_keys($data))->shouldReturn($data);
    }

    public function it_should_return_has()
    {
        $this->has('doesntExist')->shouldReturn(false);
        $this->set('test', 'data');
        $this->has('test')->shouldReturn(true);
    }

    public function it_should_delete()
    {
        $this->set('test', 'data');
        $this->delete('test')->shouldReturn(true);
        $this->get('test')->shouldBeNull();
    }

    public function it_should_delete_multiple()
    {
        $data = [
            'key1' => uniqid('key1'),
            'key2' => uniqid('key2'),
            'key3' => uniqid('key3'),
        ];
        $this->setMultiple($data, 3600);
        $this->deleteMultiple(['key1', 'key3'])->shouldReturn(true);
        $this->get('key1')->shouldReturn(null);
        $this->get('key3')->shouldReturn(null);
        $this->get('key2')->shouldReturn($data['key2']);
    }

    public function it_should_clear()
    {
        $this->set('test', 'moo');
        $this->clear()->shouldReturn(true);
        $this->get('test')->shouldBeNull();
    }


}
