<?php

namespace Colloquy\Exceptions;

use RuntimeException;

class ContextCreationFailedException extends RuntimeException
{
    public function __construct(string $id, string $fileName)
    {
        parent::__construct(sprintf(
            'Creation of context %s failed. File %s could not be created. Check if you have correct permissions.',
            $id,
            $fileName
        ));
    }
}
