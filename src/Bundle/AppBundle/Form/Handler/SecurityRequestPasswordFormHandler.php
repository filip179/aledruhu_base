<?php

namespace AppBundle\Form\Handler;

use AppVerk\UserBundle\Component\TokenGeneratorInterface;
use AppVerk\UserBundle\Doctrine\UserManager;
use AppBundle\Entity\User;
use AppBundle\Form\Model\SecurityRequestPassword;
use Component\Form\Handler\AbstractFormHandler;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class SecurityRequestPasswordFormHandler extends AbstractFormHandler {
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var EngineInterface
     */
    private $templating;
    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;
    /**
     * @var RouterInterface
     */
    private $router;

    private $mailerUser;

    public function __construct(
        $mailerUser,
        UserManager $userManager,
        \Swift_Mailer $mailer,
        EngineInterface $templating,
        TokenGeneratorInterface $tokenGenerator,
        RouterInterface $router
    )
    {
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->tokenGenerator = $tokenGenerator;
        $this->router = $router;
        $this->mailerUser = $mailerUser;
    }

    protected function success()
    {
        /** @var SecurityRequestPassword $data */
        $data = $this->form->getData();

        /** @var User $user */
        $user = $this->userManager->findUserByEmail($data->getEmail());
        if (!$user) {
            $this->addFormError('handler.request_password.user_not_exist', ['%email%' => $data->getEmail()]);

            return false;
        }

        $user->setPasswordRequestedAt(new \DateTime());
        $user->setPasswordRequestToken($this->tokenGenerator->generateToken());
        $this->userManager->updateUser($user);

        $this->sendMessage($user);

        return true;
    }

    /**
     * @param $user
     *
     * @return int
     */
    protected function sendMessage(User $user)
    {
        $resettingFormUrl = $this->router->generate(
            'security_password_reset',
            ['token' => $user->getPasswordRequestToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $message = (new \Swift_Message('Żądanie zmiany hasła'))
            ->setFrom($this->mailerUser)
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(
                    'AppBundle::emails/password_reset.html.twig',
                    [
                        'name' => $user->getUsername(),
                        'url' => $resettingFormUrl,
                    ]
                ),
                'text/html'
            );

        return $this->mailer->send($message);
    }
}
