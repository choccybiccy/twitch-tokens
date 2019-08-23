<?php

namespace Twitch\Auth\Token;

/**
 * Interface VerifierInterface.
 */
interface VerifierInterface
{
    /**
     * @param string $token
     *
     * @return Token|null
     */
    public function verifyToken(string $token);
}
