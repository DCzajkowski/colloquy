<?php

namespace Tests\Fakes;

/**
 * Class TestClass
 *
 * @package Tests\Fakes
 * @ColloquyContext('strategy')
 */
class TestClass
{
    /**
     * @var string A variable holding a name #uselessComment
     */
    private $name;

    /**
     * A variable holding a name #uselessComment
     *
     * @type int
     */
    private $age = 10;

    /** @ColloquyPersist */
    private $height;

    /** @ColloquyPersist('user-weight') */
    private $weight;

    /**
     * @ColloquyPersist
     * @type int
     */
    private $wage;
}
