<?php

namespace Colloquy;

interface IdentifierResolverInterface
{
    public function get($object): string;
}
