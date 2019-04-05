<?php

namespace AppBundle\Twig;

class SidebarExtension extends \Twig_Extension {
    const SIDEBAR_TEMPLATE = '@App/template/partials/darkblue/sidebar_menu.html.twig';
    /**
     * @var array
     */
    private $menu;

    public function __construct($menu)
    {
        $this->menu = $menu;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'sidebar', [$this, 'getSidebar'], [
                    'needs_environment' => true,
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    public function getSidebar(\Twig_Environment $environment)
    {
        return $environment->render(
            self::SIDEBAR_TEMPLATE,
            [
                'menu' => $this->menu,
            ]
        );
    }
}
