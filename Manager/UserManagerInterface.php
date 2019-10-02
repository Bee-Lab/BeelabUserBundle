<?php

namespace Beelab\UserBundle\Manager;

use Beelab\UserBundle\User\UserInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

interface UserManagerInterface extends LightUserManagerInterface
{
    /**
     * List of users (can be paginated).
     *
     * @param int    $page
     * @param int    $limit
     * @param string $sortBy
     *
     * @return mixed \Knp\Component\Pager\Pagination\PaginationInterface or array
     */
    public function getList(int $page = 1, int $limit = 20, string $sortBy = 'email');

    /**
     * Find user.
     *
     * @param string $email
     *
     * @return UserInterface|null
     */
    public function loadUserByUsername(string $email): ?UserInterface;

    /**
     * Find user by id.
     *
     * @param mixed $id
     *
     * @return UserInterface
     */
    public function get($id): UserInterface;

    /**
     * Delete user.
     *
     * @param UserInterface $user
     * @param bool          $flush
     */
    public function delete(UserInterface $user, bool $flush = true): void;

    /**
     * Manual authentication.
     *
     * @param UserInterface $user
     * @param Request       $request
     * @param string        $firewall firewall name (see your security.yml config file)
     * @param bool          $logout   whether to logout before login
     */
    public function authenticate(UserInterface $user, Request $request, string $firewall = 'main', bool $logout = false): void;

    /**
     * Get QueryBuilder.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder(): QueryBuilder;
}
