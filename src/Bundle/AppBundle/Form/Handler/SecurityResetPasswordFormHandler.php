<?php

namespace AppBundle\Form\Handler;

use AppVerk\UserBundle\Doctrine\UserManager;
use AppBundle\Form\Model\SecurityResetPassword;
use Component\Form\Handler\AbstractFormHandler;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityResetPasswordFormHandler extends AbstractFormHandler {
    /**
     * @var UserManager
     */
    private $userManager;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    public function __construct(UserManager $userManager, UserPasswordEncoderInterface $encoder)
    {
        $this->userManager = $userManager;
        $this->encoder = $encoder;
    }

    /**
     * Sending resetting password message to admin user
     */
    protected function success()
    {
        /** @var SecurityResetPassword $data */
        $data = $this->form->getData();

        if ($data->getPassword() !== $data->getPasswordRepeat()) {
            $this->addFormError('handler.reset_password.passwords_must_be_identical');

            return false;
        }

        $user = $data->getUser();

        $encodedPassword = $this->encoder->encodePassword($user, $data->getPassword());

        $user->setPasswordRequestedAt(null);
        $user->setPassword($encodedPassword);
        $user->setPasswordRequestToken(null);
        $this->userManager->updateUser($user);

        return true;
    }
}
