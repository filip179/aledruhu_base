<?php

namespace AppBundle\Entity;

use AppBundle\Util\PrimaryKeyTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class Picture
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PictureRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Picture {

    use SoftDeleteableEntity;
    use TimestampableEntity;
    use PrimaryKeyTrait;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @var Place
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place", inversedBy="pictures")
     */
    private $place;
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="pictures")
     * @ORM\JoinColumn(name="uploaded_by", referencedColumnName="id", onDelete="SET NULL")
     */
    private $uploadedBy;
    
    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $image;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image)
    {
        $this->image = $image;
    }
    


    /**
     * @return Place
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @param Place $place
     */
    public function setPlace(Place $place)
    {
        $this->place = $place;
    }

    /**
     * @return User
     */
    public function getUploadedBy()
    {
        return $this->uploadedBy;
    }

    /**
     * @param User $uploadedBy
     */
    public function setUploadedBy(User $uploadedBy)
    {
        $this->uploadedBy = $uploadedBy;
    }

    public function __toString()
    {
        return $this->name;
    }

}