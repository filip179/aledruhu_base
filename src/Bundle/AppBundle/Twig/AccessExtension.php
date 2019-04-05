<?php

namespace AppBundle\Twig;

use AppBundle\Entity\User;
use AppVerk\UserBundle\Service\Acl\AclProvider;
use Symfony\Component\Routing\Router;

class AccessExtension extends \Twig_Extension {

    const SIDEBAR_TEMPLATE = '@App/template/partials/darkblue/sidebar_menu.html.twig';
    /**
     * @var AclProvider
     */
    private $aclProvider;

    /**
     * @var Router
     */
    private $router;

    public function __construct(AclProvider $aclProvider, Router $router)
    {
        $this->aclProvider = $aclProvider;
        $this->router = $router;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'has_access', [$this, 'checkAccess'], [
                    'needs_environment' => false,
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    public function checkAccess(User $user, $route)
    {
        $controller = $this->router->getRouteCollection()->get($route)->getDefault('_controller');
        $arr = explode('\\', $controller);
        $controller = end($arr);
        return $this->aclProvider->isGranted($user, $controller);
    }
}
