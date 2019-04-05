<?php

namespace AppBundle\Doctrine;

use AppVerk\UserBundle\Doctrine\RoleManager;

class AdminRoleManager extends RoleManager {
    public function getRolesQuery(array $filters = [])
    {
        return $this->getRepository()->getRolesQuery($filters);
    }

    public function getRolesCount()
    {
        return $this->getRepository()->getRolesCount();
    }
}
