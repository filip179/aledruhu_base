<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\PictureManager;
use AppBundle\Entity\Picture;
use AppBundle\Entity\Place;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class PictureController
 * @package AppBundle\Controller
 * @Route("pictures")
 */
class PictureController extends BaseController
{
    /**
     * @Route("/{place}/delete/{picture}", name="picture_delete")
     * @Method("GET")
     * @param Place $place
     * @param Picture $picture
     * @param PictureManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Place $place, Picture $picture, PictureManager $manager)
    {
        $manager->remove($picture);

        return $this->redirectToRoute('place_edit', ['place' => $place->getId()]);
    }
}
