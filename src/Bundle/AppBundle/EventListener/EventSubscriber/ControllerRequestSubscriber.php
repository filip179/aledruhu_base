<?php

namespace AppBundle\EventListener\EventSubscriber;

use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ControllerRequestSubscriber implements EventSubscriberInterface {
    /**
     * @var User
     */
    private $user;

    /** @var  Router */
    private $router;

    public function __construct(TokenStorage $tokenStorage, Router $router)
    {
        $this->user = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(FilterControllerEvent $controllerEvent)
    {
        $pathInfo = $controllerEvent->getRequest()->getPathInfo();

        if (($this->user instanceof User) === false && preg_match('/\/admin/', $pathInfo) && !preg_match(
                '/\/admin\/login/',
                $pathInfo
            ) && !preg_match('/\/admin\/password/', $pathInfo) && $controllerEvent->isMasterRequest()) {
            $loginUrl = $this->router->generate('security_login');

            $controllerEvent->setController(
                function () use ($loginUrl) {
                    return new RedirectResponse($loginUrl);
                }
            );
        }
    }
}
