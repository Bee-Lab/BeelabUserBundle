<?php

namespace Beelab\UserBundle\Test;

use Beelab\UserBundle\Entity\User;

/**
 * UserStub
 */
class UserStub extends User
{
    public function __construct()
    {
        parent::__construct();
        $this->id = 42;
        $this->email = 'test@example.org';
    }
}