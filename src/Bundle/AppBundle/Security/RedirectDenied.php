<?php

namespace AppBundle\Security;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\RouterInterface;

class RedirectDenied
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @var GetResponseForExceptionEvent $event
     * @return null
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // If not a HttpNotFoundException ignore
        if (!$event->getException() instanceof AccessDeniedHttpException) {
            return;
        }

        // Create redirect response with url for the home page
        $response = new RedirectResponse($this->router->generate('dashboard'));

        // Set the response to be processed
        $event->setResponse($response);
    }

}