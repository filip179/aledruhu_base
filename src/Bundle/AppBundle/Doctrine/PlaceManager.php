<?php

namespace AppBundle\Doctrine;


use AppBundle\Entity\Picture;
use AppBundle\Entity\Place;
use AppBundle\Repository\PlaceRepository;
use AppVerk\UserBundle\Component\AbstractManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PlaceManager extends AbstractManager
{
    private $rootDir;

    public function __construct(string $className, ObjectManager $objectManager, string $rootDir = null)
    {
        parent::__construct($className, $objectManager);
        $this->rootDir = $rootDir;
    }

    public function getPlacesForMap($filter = null)
    {
        return $this->getRepository()->getPlacesForMap($filter);
    }

    /**
     * @return PlaceRepository
     */
    public function getRepository()
    {
        return $this->objectManager->getRepository($this->className);
    }

    public function getPlacesQuery(array $filters = [])
    {
        return $this->getRepository()->getPlacesQuery($filters);
    }

    public function getPlacesCount()
    {
        return $this->getRepository()->getPlacesCount();
    }

    public function update(Place $place)
    {
        $files = $place->getPictures();

        /**
         * @var $file UploadedFile
         */
        foreach ($files as $file) {

            $data = file_get_contents($file->getPathname());
            $base64 = 'data:image/' . $file->getClientMimeType() . ';base64,' . base64_encode($data);

            $picture = new Picture();
            $picture->setPlace($place);
            $picture->setImage($base64);
            $picture->setName(date('Y-m-d_H:i:s') . '_' . $place->getId());
            $picture->setUploadedBy($place->getModifiedBy());

            $this->objectManager->persist($picture);
        }
        $place->setPictures([]);
        $this->objectManager->persist($place);
        $this->objectManager->flush();
    }

    public function remove(Place $place)
    {
        $this->objectManager->remove($place);
        $this->objectManager->flush();
    }
}
