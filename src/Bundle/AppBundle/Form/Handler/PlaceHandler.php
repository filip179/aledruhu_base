<?php

namespace AppBundle\Form\Handler;

use AppBundle\Doctrine\PlaceManager;
use AppBundle\Entity\Place;
use Component\Form\Handler\AbstractFormHandler;

class PlaceHandler extends AbstractFormHandler {
    /** @var PlaceManager */
    private $manager;

    public function __construct(PlaceManager $placeManager)
    {
        $this->manager = $placeManager;
    }

    protected function success()
    {
        /** @var Place $place */
        $place = $this->form->getData();

        $this->manager->update($place);

        return true;
    }
}
