<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\PlaceManager;
use AppBundle\Doctrine\PlaceTypeManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/maps")
 */
class MapsController extends BaseController
{
    /**
     * @Route("/search", name="maps_search")
     * @Method("GET")
     * @param PlaceManager $placeManager
     * @param PlaceTypeManager $placeTypeManager
     * @param Request $request
     * @return Response
     */
    public function searchAction(PlaceManager $placeManager, PlaceTypeManager $placeTypeManager, Request $request)
    {
        $filter = $request->get('placeType') ?? null;
        $placeTypes = $placeTypeManager->getPlacesForChoice();
        $places = $placeManager->getPlacesForMap($filter);
        return $this->render('AppBundle::controller/map/search.html.twig', ['places' => $places, 'placeTypes' => $placeTypes]);
    }

}