<?php

namespace AppBundle\Doctrine;


use AppVerk\UserBundle\Component\AbstractManager;
use AppVerk\UserBundle\Component\ManagerInterface;
use AppBundle\Repository\PlaceTypeRepository;

class PlaceTypeManager extends AbstractManager{
    
    public function getPlacesForChoice()
    {
        return $this->getRepository()->getAllPlaceTypes();
    }

    /**
     * @return PlaceTypeRepository
     */
    public function getRepository()
    {
        return $this->objectManager->getRepository($this->className);
    }
}
