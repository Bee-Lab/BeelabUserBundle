<?php

namespace Beelab\UserBundle\Twig;

use Twig_Extension;

class BeelabUserTwigExtension extends Twig_Extension
{
    protected $layout, $theme;

    /**
     * @param string $layout
     * @param string $theme
     */
    public function __construct($layout, $theme)
    {
        $this->layout = $layout;
        $this->theme = $theme;
    }

    /**
     * {@inheritDoc}
     */
    public function getGlobals()
    {
        return array(
            'beelab_user_layout' => $this->layout,
            'beelab_user_theme'  => $this->theme,
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