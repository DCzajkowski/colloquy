<?php

namespace Colloquy\Exceptions;

use RuntimeException;

class ContextAlreadyExistsException extends RuntimeException
{
    public function __construct(string $identifier)
    {
        parent::__construct(sprintf(
            'A context with name %s already exists',
            $identifier
        ));
    }
}
