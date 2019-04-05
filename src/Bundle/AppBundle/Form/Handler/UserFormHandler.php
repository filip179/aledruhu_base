<?php

namespace AppBundle\Form\Handler;

use AppBundle\Doctrine\AdminUserManager;
use AppBundle\Entity\User;
use Component\Form\Handler\AbstractFormHandler;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFormHandler extends AbstractFormHandler {
    /** @var AdminUserManager */
    private $userManager;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    public function __construct(AdminUserManager $userManager, UserPasswordEncoderInterface $encoder)
    {
        $this->userManager = $userManager;
        $this->encoder = $encoder;
    }

    protected function success()
    {
        /** @var User $user */
        $userData = $this->form->getData();

        $userExist = $this->userManager->findUserByUsername($userData->getUsername());
        if (($userExist instanceof User && !$userData->getId()) || ($userExist instanceof User && $userExist->getId() != $userData->getId())) {
            $this->addFormError('handler.user.username_exists');

            return false;
        }

        $userExist = $this->userManager->findUserByEmail($userData->getEmail());

        if (($userExist instanceof User && !$userData->getId()) || ($userExist instanceof User && $userExist->getId() != $userData->getId())) {
            $this->addFormError('handler.user.email_exists');

            return false;
        }

        if (!$this->form->get('plainPassword')->getData()) {
            $this->userManager->updateUser($userData);

            return true;
        }

        $plainPassword = $this->form->get('plainPassword')->getData();
        $user = $this->userManager->findUserByUsername($userData->getUsername());

        if ($user) {
            $encodedPasswordChecker = $this->encoder->encodePassword($userData, $plainPassword);

            if ($encodedPasswordChecker === $user->getPassword()) {
                $this->addFormError('handler.user.password_must_be_different_from_old');

                return false;
            }
        }

        $salt = $this->userManager->generateSalt();

        $userData->setSalt($salt);
        $encodedPassword = $this->encoder->encodePassword($userData, $plainPassword);
        $userData->setPassword($encodedPassword);
        $this->userManager->updateUser($userData);

        return true;
    }
}
