<?php

namespace Beelab\UserBundle\Twig;

use Twig_Extension;

/**
 * This extension is used to register some global variables
 */
class BeelabUserTwigExtension extends Twig_Extension
{
    protected $layout;
    protected $route;

    /**
     * @param string $layout layout name (for "extends" statement)
     * @param string $route  route used in index.html.twig
     */
    public function __construct($layout, $route)
    {
        $this->layout = $layout;
        $this->route = $route;
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
    public function getName()
    {
        return 'beelab_user_twig_extension';
    }
}
