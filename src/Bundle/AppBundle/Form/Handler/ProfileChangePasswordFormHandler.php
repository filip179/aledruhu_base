<?php

namespace AppBundle\Form\Handler;

use AppVerk\UserBundle\Doctrine\UserManager;
use AppBundle\Entity\User;
use AppBundle\Form\Model\ProfileChangePassword;
use Component\Form\Handler\AbstractFormHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class ProfileChangePasswordFormHandler extends AbstractFormHandler {
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /** @var EncoderFactoryInterface */
    private $encoderFactory;

    public function __construct(
        UserManager $userManager,
        TokenStorageInterface $tokenStorage,
        EncoderFactoryInterface $encoderFactory
    )
    {
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * Sending resetting password message to admin user
     */
    protected function success()
    {
        /** @var ProfileChangePassword $data */
        $data = $this->form->getData();

        /** @var User $user */
        $user = $this->getUser();

        $encoder = $this->encoderFactory->getEncoder($user);
        $isValid = $encoder->isPasswordValid($user->getPassword(), $data->getOldPassword(), $user->getSalt());

        if (!$isValid) {
            $this->addFormError('handler.change_password.old_password.invalid');

            return false;
        }

        if ($data->getNewPassword() !== $data->getNewPasswordRepeat()) {
            $this->addFormError('handler.change_password.new_password.not_same');

            return false;
        }

        $encodedPassword = $encoder->encodePassword($data->getNewPassword(), $user->getSalt());
        $user->setPassword($encodedPassword);

        $this->userManager->updateUser($user);

        return true;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}
