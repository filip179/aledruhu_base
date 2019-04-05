<?php

namespace AppBundle\Entity;

use AppBundle\Util\PrimaryKeyTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Comment
{
    use PrimaryKeyTrait;
    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $user;

    /**
     * @ORM\Column(name="parent_id", type="integer")
     */
    private $parentId;

    /**
     * @var string
     * @ORM\Column(name="parent_entity", type="string", length=255)
     */
    private $parentEntity;

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
        return $this;
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function setParentEntity($parentEntity)
    {
        $this->parentEntity = $parentEntity;

        return $this;
    }

    public function getParentEntity()
    {
        return $this->parentEntity;
    }

    public static function getEntityByKey($key)
    {
        $entities = [ 'comment' => Comment::class, 'place' => Place::class ];
        if( !array_key_exists($key, $entities)) return;

        return $entities[$key];
    }
}
