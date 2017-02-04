<?php

namespace Beelab\UserBundle\Twig;

use Knp\Component\Pager\PaginatorInterface;
use Twig_Extension;
use Twig_Extension_GlobalsInterface;
use Twig_SimpleFunction;

/**
 * This extension is used to register some global variables.
 * Also, add a function to check if Knp Paginator is enabled.
 */
class BeelabUserTwigExtension extends Twig_Extension implements Twig_Extension_GlobalsInterface
{
    protected $layout;
    protected $route;
    protected $hasPaginator = false;

    /**
     * @param string             $layout    layout name (for "extends" statement)
     * @param string             $route     route used in index.html.twig
     * @param PaginatorInterface $paginator
     */
    public function __construct(string $layout, string $route, PaginatorInterface $paginator = null)
    {
        $this->layout = $layout;
        $this->route = $route;
        $this->hasPaginator = !is_null($paginator);
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals(): array
    {
        return [
            'beelab_user_layout' => $this->layout,
            'beelab_user_route' => $this->route,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new Twig_SimpleFunction('has_paginator', [$this, 'hasPaginator']),
        ];
    }

    /**
     * @return bool
     */
    public function hasPaginator(): bool
    {
        return $this->hasPaginator;
    }
}
