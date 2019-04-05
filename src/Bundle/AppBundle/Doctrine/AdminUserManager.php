<?php

namespace AppBundle\Doctrine;

use AppVerk\UserBundle\Doctrine\UserManager;
use AppVerk\UserBundle\Model\UserInterface;
use Component\Doctrine\UserProviderInterface;

class AdminUserManager extends UserManager implements UserProviderInterface {
    public function getUsersQuery(array $filters = [])
    {
        return $this->getRepository()->getUsersQuery($filters);
    }

    public function getUsersCount()
    {
        return $this->getRepository()->getUsersCount();
    }

    public function findUserByEmail(string $email)
    {
        return $this->getRepository()->findUserByEmail($email);
    }

    public function findUserByUsername(string $username)
    {
        return $this->getRepository()->findUserByUsername($username);
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->getRepository()->loadUserByUsername($username);
    }
}
