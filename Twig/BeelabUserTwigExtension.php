<?php

namespace Beelab\UserBundle\Twig;

use Twig_Extension;

class BeelabUserTwigExtension extends Twig_Extension
{
    protected $layout;

    /**
     * @param string $layout
     */
    public function __construct($layout)
    {
        $this->layout = $layout;
    }

    /**
     * {@inheritDoc}
     */
    public function getGlobals()
    {
        return array(
            'beelab_user_layout' => $this->layout,
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