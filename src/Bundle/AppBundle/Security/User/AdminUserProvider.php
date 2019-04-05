<?php

namespace AppBundle\Security\User;

use AppVerk\UserBundle\Security\User\AbstractUserProvider;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminUserProvider extends AbstractUserProvider {
    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        /** @var User $user */
        $user = $this->getUserRepository()->findOneBy(['username' => $username]);

        if (!$user ) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }
}
