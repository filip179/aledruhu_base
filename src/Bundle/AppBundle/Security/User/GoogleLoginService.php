<?php

namespace AppBundle\Security\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

/**
 * Class GoogleLoginService
 * @package AppBundle\Security\User
 *
 * @TODO REFACTOR THIS COPY & PASTE PIECE OF ... CODE
 */
class GoogleLoginService
{
    /**
     * @var Router
     */
    private $generator;

    public function __construct(Router $router)
    {
        $this->generator = $router;
    }

    public function getURL()
    {
        $client = new \Google_Client();
        $client->setApplicationName('');
        $client->setScopes([
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
        ]);
        $client->setClientId('');
        $client->setClientSecret('');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->setRedirectUri($this->generator->generate('security_check', [], Router::ABSOLUTE_URL));
        return $client->createAuthUrl();
    }

    public function getDetails(Request $request)
    {
        $client = new \Google_Client();
        $client->setApplicationName('');
        $client->setScopes([
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
        ]);
        $client->setClientId('');
        $client->setClientSecret('');
        $client->setRedirectUri($this->generator->generate('security_check', [], Router::ABSOLUTE_URL));
        $service = new \Google_Service_Oauth2($client);
        $code = $client->authenticate($request->query->get('code'));
        $client->setAccessToken($code);
        return $service->userinfo->get();
    }
}