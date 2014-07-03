<?php

namespace Beelab\UserBundle\Twig;

use Twig_Extension;

/**
 * This extension is used to register some global variables
 */
class BeelabUserTwigExtension extends Twig_Extension
{
    protected $layout, $theme, $route;

    /**
     * @param string $layout layout name (for "extends" statement)
     * @param string $theme  theme name (for "form_theme" statement)
     * @param string $route  route used in index.html.twig
     */
    public function __construct($layout, $theme, $route)
    {
        $this->layout = $layout;
        $this->theme = $theme;
        $this->route = $route;
    }

    /**
     * {@inheritDoc}
     */
    public function getGlobals()
    {
        return array(
            'beelab_user_layout' => $this->layout,
            'beelab_user_theme'  => $this->theme,
            'beelab_user_route'  => $this->route,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'beelab_user_twig_extension';
    }
}
