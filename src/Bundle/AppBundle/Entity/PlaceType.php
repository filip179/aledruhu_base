<?php

namespace AppBundle\Entity;

use AppBundle\Util\PrimaryKeyTrait;
use Component\Doctrine\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlaceTypeRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class PlaceType implements EntityInterface {
    use PrimaryKeyTrait;
    use TimestampableEntity;
    use SoftDeleteableEntity;

    /**
     * @var Place[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Place", mappedBy="placeType")
     */
    private $places;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    private $modifiedBy;

    /**
     * @return Place[]
     */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * @param Place[] $places
     */
    public function setPlaces(array $places)
    {
        $this->places = $places;
    }

    /**
     * @param Place $place
     */
    public function addPlace(Place $place)
    {
        $this->places[] = $place;
    }

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
     * @return User
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * @param User $modifiedBy
     */
    public function setModifiedBy(User $modifiedBy)
    {
        $this->modifiedBy = $modifiedBy;
    }

    public function __toString()
    {
        return $this->name;
    }


}