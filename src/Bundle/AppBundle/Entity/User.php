<?php

namespace AppBundle\Entity;

use AppVerk\UserBundle\Entity\User as AbstractUser;
use Component\Doctrine\EntityInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends AbstractUser implements EntityInterface {
    /**
     *  resetting token valid 2 days
     */
    const TOKEN_TTL = 172800;
    public static $userRoles = [
        self::ROLE_ADMIN => 'Administrator',
    ];
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Role", inversedBy="users")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $role;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="user", cascade="remove")
     */
    protected $comments;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Picture", mappedBy="uploadedBy", cascade="detach")
     */
    protected $pictures;

    public static function getUserRolesForChoice()
    {
        $roles = self::$userRoles;
        unset($roles[self::ROLE_ADMIN]);

        return $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        list(
            $this->password,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            ) = $data;
    }

    public function getRoleName()
    {
        return $this->getRole()->getName();
    }

    public function __toString()
    {
        return parent::__toString() ?? 'Administracja';
    }
}
