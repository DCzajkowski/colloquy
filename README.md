# Colloquy
[![Latest Stable Version](https://poser.pugx.org/dczajkowski/colloquy/version)](https://packagist.org/packages/dczajkowski/colloquy)
[![License](https://poser.pugx.org/dczajkowski/colloquy/license)](https://packagist.org/packages/dczajkowski/colloquy)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/DCzajkowski/colloquy/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/DCzajkowski/colloquy/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/DCzajkowski/colloquy/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/DCzajkowski/colloquy/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/DCzajkowski/colloquy/badges/build.png?b=master)](https://scrutinizer-ci.com/g/DCzajkowski/colloquy/build-status/master)

A framework-agnostic package for managing persistent conversation contexts.

# Installation
```bash
composer install dczajkowski/colloquy
```

# Usage

## Using in auto-mode with annotations
**Identifier resolver declaration**
```php
<?php

namespace App;

class SessionIdentifierResolver implements \Colloquy\IdentifierResolverInterface
{
    public function get($controller): string
    {
        return // code to get session id 
    }
}
```

**Context binding**
```php
<?php

\Colloquy\Colloquy::bind(
    'session',
    new \App\SessionIdentifierResolver,
    new \Colloquy\Drivers\FileDriver($pathToWritableDirectory)
);
```

**Use in a controller**
```php
<?php

namespace App\Http\Controllers;

/** @ColloquyContext('session') */
class FormController
{
    use \Colloquy\ColloquyContexts;
    
    /** @ColloquyPersist */
    protected $user;

    /** @ColloquyBegin */
    private function step1()
    {
        $this->user = new \App\Models\User;
    }

    public function step2()
    {
        $this->user->name = 'John';
    }

    /** @ColloquyEnd */
    private function step3()
    {
        echo $this->user->name; // John
    }
}
```

## Manual Use

```php
<?php

use Colloquy\Colloquy;
use Colloquy\Drivers\FileDriver;

class User
{
   private $name;
   
   public function __construct(string $name) {
       $this->name = $name;
   }
}

/** Starting a Conversation */

$wrapper = new Colloquy(new FileDriver('storage'));

$homeContext = $wrapper->context('Home');
$formContext = $wrapper->context('Form');

/** Primitive types */

$homeContext->add('Joe', 'name');
$homeContext->add('ilovecats', 'password');
$formContext->add('Jane', 'name');

$name = $homeContext->get('name'); // Joe

$homeContext->set('John', 'name');

$name = $homeContext->get('name'); // John

/** Objects */

$user = new User('Jack');

$homeContext->add($user, 'user');

var_dump($user); // User { name: "Jack" }

$user = $homeContext->get('user');

var_dump($user); // User { name: "Jack" }

/** Ending a Conversation */

$wrapper->end('Form');
$wrapper->end('Home');
```

# Contribution
Contributions are very welcome. If you want, just drop a PR with any feature you'd like to see.

# Authors
The library was made by [Dariusz Czajkowski](https://dczajkowski.com), Grzegorz Tłuszcz, Aleksander Kuźma, Karol Piwnicki.

# License
The Colloquy package is open-sourced software licensed under the MIT license.
