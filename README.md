# twitch-tokens [![Build Status](https://travis-ci.org/choccybiccy/twitch-tokens.svg?branch=master)](https://travis-ci.org/choccybiccy/twitch-tokens)
A library to verify Twitch JWT tokens.

## Requirements

- PHP 7.1+
- ext-json

## Installation

```
composer require choccybiccy/twitch-tokens
```

## Usage

```
use Twitch\Auth\Token\Verifier;

$verifier = new Verifier;
if ($token = $verifier->verifyToken('token-string')) {
    $id = $token->get('sub');
    var_dump($token->toArray());
}
```

## Contributing

Make your changes, and ensure that appropriate tests have been created
and that the test suite passes.

```
./vendor/bin/phpspec
```

## Thanks

Special mention for help and guidance provided by:

- https://github.com/kreait/firebase-tokens-php
- https://web-token.spomky-labs.com/
- https://dev.twitch.tv/docs/authentication/
