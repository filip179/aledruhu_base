<?php

namespace AppBundle\Doctrine;


use AppBundle\Entity\Picture;
use AppBundle\Repository\PictureRepository;
use AppVerk\UserBundle\Component\AbstractManager;

class PictureManager extends AbstractManager {

    /**
     * @return PictureRepository
     */
    public function getRepository()
    {
        return $this->objectManager->getRepository($this->className);
    }

    public function update(Picture $picture)
    {
        $this->objectManager->persist($picture);
        $this->objectManager->flush();
    }

    public function remove(Picture $picture)
    {
        $this->objectManager->remove($picture);
        $this->objectManager->flush();
    }
}
