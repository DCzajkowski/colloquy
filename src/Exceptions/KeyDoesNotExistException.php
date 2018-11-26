<?php

namespace Colloquy\Exceptions;

use RuntimeException;

class KeyDoesNotExistException extends RuntimeException
{
    public function __construct(string $context, string $key)
    {
        parent::__construct(sprintf('Key %s was not found in context %s.', $key, $context));
    }
}
