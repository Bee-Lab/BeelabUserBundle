<?php

namespace Beelab\UserBundle\Manager;

use Beelab\UserBundle\User\UserInterface;

interface LightUserManagerInterface
{
    /**
     * Get new instance of User.
     *
     * @return UserInterface
     */
    public function getInstance(): UserInterface;

    /**
     * Create new user.
     *
     * @param UserInterface $user
     * @param bool          $flush
     */
    public function create(UserInterface $user, bool $flush = true): void;

    /**
     * Update existing user.
     *
     * @param UserInterface $user
     * @param bool          $flush
     */
    public function update(UserInterface $user, bool $flush = true): void;
}
