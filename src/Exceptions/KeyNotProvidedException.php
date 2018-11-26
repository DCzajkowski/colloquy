<?php

namespace Colloquy\Exceptions;

use RuntimeException;

class KeyNotProvidedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'When adding data to a context you must provide a key if the value is of primitive type.'
        );
    }
}
