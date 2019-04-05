<?php

namespace AppBundle\Form\Handler;

use AppVerk\UserBundle\Doctrine\UserManager;
use AppBundle\Entity\User;
use AppBundle\Form\Model\ProfileEdit;
use Component\Form\Handler\AbstractFormHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProfileEditFormHandler extends AbstractFormHandler {
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(UserManager $userManager, TokenStorageInterface $tokenStorage)
    {
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Sending resetting password message to admin user
     */
    protected function success()
    {
        /** @var ProfileEdit $data */
        $data = $this->form->getData();

        $user = $this->getUser();
        $user->setFirstName($data->getFirstname());
        $user->setLastName($data->getLastname());

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
