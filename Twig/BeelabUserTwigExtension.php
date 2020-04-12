<?php

namespace Beelab\UserBundle\Twig;

use Knp\Component\Pager\PaginatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

/**
 * This extension is used to register some global variables.
 * Also, add a function to check if Knp Paginator is enabled.
 */
class BeelabUserTwigExtension extends AbstractExtension implements GlobalsInterface
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
        $this->hasPaginator = null !== $paginator;
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
            new TwigFunction('has_paginator', [$this, 'hasPaginator']),
        ];
    }

    public function hasPaginator(): bool
    {
        return $this->hasPaginator;
    }
}
