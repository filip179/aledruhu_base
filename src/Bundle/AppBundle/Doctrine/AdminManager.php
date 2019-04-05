<?php

namespace AppBundle\Doctrine;

use AppVerk\UserBundle\Doctrine\UserManager;
use AppBundle\Entity\User;
use Component\Doctrine\UserProviderInterface;

class AdminManager extends UserManager implements UserProviderInterface {
    public function findAdminByEmail(string $email)
    {
        $user = $this->getRepository()->findOneBy(
            [
                'email' => $email,
            ]
        );

        if (!$user || !$user->hasRole(User::ROLE_ADMIN)) {
            return null;
        }

        return $user;
    }

    public function findAdminByToken($token)
    {
        if (!$token) {
            return false;
        }

        /** @var User $user */
        $user = $this->getRepository()->findOneBy(
            [
                'passwordRequestToken' => $token,
            ]
        );

        if (!$user || !$user->isPasswordRequestNonExpired()) {
            return false;
        }

        return $user;
    }

    public function findAdminByPassword($password)
    {
        return $this->getRepository()->findOneBy(
            [
                'password' => $password,
            ]
        );
    }
}
