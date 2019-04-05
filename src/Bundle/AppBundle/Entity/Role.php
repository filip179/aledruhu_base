<?php

namespace AppBundle\Entity;

use AppVerk\UserBundle\Entity\Role as AbstractRole;
use Component\Doctrine\EntityInterface;
use Component\Entity\DeletableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoleRepository")
 */
class Role extends AbstractRole implements EntityInterface, DeletableInterface {
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="role")
     */
    protected $users;
}
