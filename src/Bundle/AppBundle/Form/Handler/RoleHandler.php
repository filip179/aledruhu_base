<?php

namespace AppBundle\Form\Handler;

use AppBundle\Doctrine\AdminRoleManager;
use AppBundle\Entity\Role;
use Component\Form\Handler\AbstractFormHandler;

class RoleHandler extends AbstractFormHandler {
    /** @var AdminRoleManager */
    private $roleManager;

    public function __construct(AdminRoleManager $roleManager)
    {
        $this->roleManager = $roleManager;
    }

    protected function success()
    {
        /** @var Role $role */
        $role = $this->form->getData();

        $roleExists = $this->roleManager->findRoleByName($role->getName());
        if (($roleExists instanceof Role && !$role->getId()) || ($roleExists instanceof Role && $roleExists->getId() != $role->getId())) {
            $this->addFormError('handler.role.name_exists');

            return false;
        }

        $permissions = $this->form->get('permissions')->getData();

        if (empty($permissions)) {
            $this->addFormError('handler.role.empty_permissions');

            return false;
        }

        $role->setCredentials($permissions);
        $this->roleManager->updateRole($role);

        return true;
    }
}
