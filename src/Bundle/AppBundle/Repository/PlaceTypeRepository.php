<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PlaceTypeRepository extends EntityRepository {

    /**
     * @return array
     */
    public function getAllPlaceTypes(): array
    {
        return $this->findAll();
    }
}
