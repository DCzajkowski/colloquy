<?php

namespace Tests\Fakes;

use Colloquy\ColloquyContexts;

/** @ColloquyContext('session') */
class TestController
{
    use ColloquyContexts;

    /**
     * @type User
     * @ColloquyPersist
     */
    public $user;

    /** @ColloquyBegin */
    private function step1()
    {
        $this->user = new User;
        $this->user->setName('John');

        return 'step1';
    }

    private function step2()
    {
        $this->user->setAge(21);

        return 'step2';
    }

    /** @ColloquyEnd */
    private function step3()
    {
        return $this->user;
    }
}
