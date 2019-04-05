<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\CommentManager;
use AppBundle\Doctrine\PlaceManager;
use AppBundle\Doctrine\PlaceTypeManager;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Place;
use AppBundle\Form\Handler\CommentHandler;
use AppBundle\Form\Handler\PlaceHandler;
use AppBundle\Form\Type\CommentType;
use AppBundle\Form\Type\PlaceType;
use AppBundle\Util\DatatableDataProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * @Route("/places")
 */
class PlaceController extends BaseController
{
    /**
     * @Route("/list", name="place_list")
     * @Method("GET")
     * @param PlaceTypeManager $placeTypeManager
     * @return Response
     */
    public function listAction(PlaceTypeManager $placeTypeManager)
    {
        $placeTypes = $placeTypeManager->getPlacesForChoice();

        return $this->render('AppBundle::controller/place/list.html.twig', [ 'placeTypes' => $placeTypes ]);
    }

    /**
     * @Route("/datatable", name="place_datatable")
     * @Method("POST")
     * @param PlaceManager $placeManager
     * @param DatatableDataProvider $datatableDataProvider
     * @param Request $request
     * @return JsonResponse
     */
    public function datatableAction(
        PlaceManager $placeManager,
        DatatableDataProvider $datatableDataProvider,
        Request $request
    )
    {
        $filters = [
            'name' => $request->get('name') ?? null,
            'city' => $request->get('city') ?? null,
            'placeType' => $request->get('placeType') ?? null,
            'capacity' => $request->get('capacity') ?? null,
            'postcode' => $request->get('postcode') ?? null,
        ];

        $query = $placeManager->getPlacesQuery($filters);
        $recordsTotal = $placeManager->getPlacesCount();

        $actions = [
            'view' => [
                'route' => 'place_view',
                'params' => [
                    'key' => 'place',
                    'value' => 'id',
                ],
            ],
            'edit' => [
                'route' => 'place_edit',
                'params' => [
                    'key' => 'place',
                    'visible' => 'invisible',
                    'value' => 'id',
                ],
            ],
            'delete' => [
                'route' => 'place_delete',
                'visible' => false,
                'params' => [
                    'key' => 'place',
                    'value' => 'id',
                ],
            ],
        ];

        $fields = [ 'id', 'name', 'postcode', 'city', 'capacity', 'placeType', 'updatedAt' ];

        $data = $datatableDataProvider->getData($query, $recordsTotal, $actions, $fields);

        return new JsonResponse($data);
    }

    /**
     * @Route("/view/{place}", name="place_view")
     * @Method({"GET"})
     * @param Place $place
     * @param CommentManager $manager
     * @param CommentHandler $handler
     * @return Response
     */
    public function viewAction(Place $place, CommentManager $manager, CommentHandler $handler)
    {
        $form = $handler->buildForm(
            CommentType::class,
            new Comment()
        )->getFormView();

        $comments = $manager->getCommentsTree($place);
        return $this->render('AppBundle::controller/place/view.html.twig', [
            'place' => $place,
            'comments' => $comments,
            'form' => $form,
            'comment_options' => [ 'comment' => Comment::class, 'place' => Place::class ]
        ]);
    }

    /**
     * @Route("/create", name="place_create")
     * @Method({"GET"})
     * @param PlaceHandler $handler
     * @return Response
     */
    public function createAction(PlaceHandler $handler)
    {
        $form = $handler->buildForm(
            PlaceType::class,
            new Place()
        )->getFormView();

        return $this->render('AppBundle::controller/place/create.html.twig', [ 'form' => $form ]);
    }

    /**
     * @Route("/edit/{place}", name="place_edit")
     * @Method({"GET", "POST"})
     * @param Place $place
     * @param PlaceHandler $handler
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction(Place $place, PlaceHandler $handler, Request $request)
    {
        $form = $handler->buildForm(
            PlaceType::class,
            $place
        )->getFormView();

        if($request->isMethod('GET')) {
            return $this->render(
                'AppBundle::controller/place/edit.html.twig',
                [
                    'form' => $form,
                    'entity' => $place,
                ]
            );
        }
        $this->addFlashMessage('success', 'place.edit.successful');

        return $this->redirectToRoute('place_list');
    }

    /**
     * @Route("/delete/{place}", name="place_delete")
     * @Method("POST")
     * @param Place $place
     * @param PlaceManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Place $place, PlaceManager $manager)
    {
        $manager->remove($place);
        $this->addFlashMessage('success', 'place.delete.successful');

        return $this->redirectToRoute('place_list');
    }

    /**
     * @Route("/postCreate", name="place_post_create")
     * @Method({"POST"})
     * @param PlaceHandler $handler
     * @param TokenStorage $tokenStorage
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function postCreateAction(PlaceHandler $handler, TokenStorage $tokenStorage)
    {
        $place = new Place();
        $place->setModifiedBy($tokenStorage->getToken()->getUser());
        $createHandler = $handler->buildForm(
            PlaceType::class,
            $place
        );

        if( !$createHandler->process()) {
            $this->addFormErrors($createHandler->getErrors());

            return $this->redirectToRoute('place_create');
        }

        $this->addFlashMessage('success', 'place.create.successful');

        return $this->redirectToRoute('place_list');
    }

    /**
     * @Route("/postEdit/{place}", name="place_post_edit")
     * @Method({"POST"})
     * @param Place $place
     * @param PlaceHandler $placeHandler
     * @param TokenStorage $tokenStorage
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function postEditAction(Place $place, PlaceHandler $placeHandler, TokenStorage $tokenStorage)
    {
        $place->setModifiedBy($tokenStorage->getToken()->getUser());
        $editHandler = $placeHandler->buildForm(PlaceType::class, $place);

        if( !$editHandler->process()) {
            $this->addFormErrors($editHandler->getErrors());

            return $this->redirectToRoute(
                'place_edit',
                [
                    'place' => $place->getId(),
                ]
            );
        }

        $this->addFlashMessage('success', 'place.edit.successful');

        return $this->redirectToRoute('place_list');
    }
}