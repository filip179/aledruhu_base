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
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlaceRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Place implements EntityInterface {
    use SoftDeleteableEntity;
    use TimestampableEntity;
    use PrimaryKeyTrait;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, nullable=false)
     */
    private $name;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $latitude;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $longitude;
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    private $modifiedBy;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $postcode;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $city;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $number;

    /**
     * @var PlaceType
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PlaceType", inversedBy="places")
     */
    private $placeType;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $owner;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $additionalContact;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $capacity;
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $additionalInfo;
    /**
     * @var Picture[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Picture", mappedBy="place")
     */
    private $pictures;

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
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param string $latitude
     */
    public function setLatitude(string $latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param string $longitude
     */
    public function setLongitude(string $longitude)
    {
        $this->longitude = $longitude;
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

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @param string $postcode
     */
    public function setPostcode(string $postcode)
    {
        $this->postcode = $postcode;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber(string $number)
    {
        $this->number = $number;
    }

    /**
     * @return PlaceType
     */
    public function getPlaceType()
    {
        return $this->placeType;
    }

    /**
     * @param PlaceType $placeType
     */
    public function setPlaceType(PlaceType $placeType)
    {
        $this->placeType = $placeType;
    }

    /**
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param string $owner
     */
    public function setOwner(string $owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return string
     */
    public function getAdditionalContact()
    {
        return $this->additionalContact;
    }

    /**
     * @param string $additionalContact
     */
    public function setAdditionalContact(string $additionalContact)
    {
        $this->additionalContact = $additionalContact;
    }

    /**
     * @return int
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @param int $capacity
     */
    public function setCapacity(int $capacity)
    {
        $this->capacity = $capacity;
    }

    /**
     * @return string
     */
    public function getAdditionalInfo()
    {
        return $this->additionalInfo;
    }

    /**
     * @param string $additionalInfo
     */
    public function setAdditionalInfo(string $additionalInfo)
    {
        $this->additionalInfo = $additionalInfo;
    }

    /**
     * @return Picture[]
     */
    public function getPictures()
    {
        return $this->pictures;
    }

    /**
     * @param Picture[] $pictures
     */
    public function setPictures(array $pictures)
    {
        $this->pictures = $pictures;
    }

    /**
     * @param Picture $picture
     */
    public function addPictures(Picture $picture)
    {
        if (!\in_array($picture, $this->pictures)) {
            $this->pictures[] = $picture;
        }
    }

}