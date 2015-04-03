<?php

namespace Beelab\UserBundle\Twig;

use Knp\Component\Pager\PaginatorInterface;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * This extension is used to register some global variables.
 * Also, add a function to check if Knp Paginator is enabled
 */
class BeelabUserTwigExtension extends Twig_Extension
{
    protected $layout;
    protected $route;
    protected $hasPaginator = false;

    /**
     * @param string             $layout    layout name (for "extends" statement)
     * @param string             $route     route used in index.html.twig
     * @param PaginatorInterface $paginator
     */
    public function __construct($layout, $route, PaginatorInterface $paginator = null)
    {
        $this->layout = $layout;
        $this->route = $route;
        $this->hasPaginator = !is_null($paginator);
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return array(
            'beelab_user_layout' => $this->layout,
            'beelab_user_route'  => $this->route,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('has_paginator', array($this, 'hasPaginator')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'beelab_user_twig_extension';
    }

    /**
     * @return boolean
     */
    public function hasPaginator()
    {
        return $this->hasPaginator;
    }
}
