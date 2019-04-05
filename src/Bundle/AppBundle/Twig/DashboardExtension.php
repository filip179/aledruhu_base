<?php

namespace AppBundle\Twig;

use AppBundle\Entity\User;
use AppVerk\UserBundle\Service\Acl\AclProvider;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class DashboardExtension extends \Twig_Extension {
    const DASHBOARD_TEMPLATE = '@App/template/partials/darkblue/dashboard.html.twig';

    /**
     * @var array
     */
    private $menu;
    /**
     * @var Router
     */
    private $router;
    /**
     * @var AclProvider
     */
    private $aclProvider;
    /**
     * @var User
     */
    private $tokenStorage;

    public function __construct($menu, Router $router, AclProvider $aclProvider, TokenStorage $tokenStorage)
    {
        $this->menu = $menu;
        $this->router = $router;
        $this->aclProvider = $aclProvider;
        $this->tokenStorage = $tokenStorage;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'dashboard', [$this, 'getDashboard'], [
                    'needs_environment' => true,
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    public function getDashboard(\Twig_Environment $environment): string
    {
        $dashboard = $this->menu;
        if (array_key_exists('dashboard', $dashboard)) {
            unset($dashboard['dashboard']);
        }

        foreach ($dashboard as $key => $menu) {
            $controller = $this->router->getRouteCollection()->get($menu['route'])->getDefault('_controller');
            $arr = explode('\\', $controller);
            $controller = end($arr);
            if (!$this->aclProvider->isGranted($this->tokenStorage->getToken()->getUser(), $controller)) {
                unset($dashboard[$key]);
            }
        }

        $dashboardCounter = count($dashboard);

        $leftDashboard = array_slice($dashboard, 0, $dashboardCounter / 2);
        $rightDashboard = array_slice($dashboard, $dashboardCounter / 2);

        return $environment->render(
            self::DASHBOARD_TEMPLATE,
            [
                'leftMenu' => $leftDashboard,
                'rightMenu' => $rightDashboard,
            ]
        );
    }
}
