<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\CommentManager;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Place;
use AppBundle\Form\Handler\CommentHandler;
use AppBundle\Form\Handler\PlaceHandler;
use AppBundle\Form\Type\CommentType;
use AppBundle\Form\Type\PlaceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * @Route("/comments")
 */
class CommentController extends BaseController
{
    /**
     * @Route("{place}/create", name="comment_create")
     * @Method({"POST"})
     * @param CommentHandler $handler
     * @param Place $place
     * @param TokenStorage $tokenStorage
     * @return Response
     */
    public function createAction(CommentHandler $handler, Place $place, TokenStorage $tokenStorage)
    {
        $comment = new Comment();
        $comment->setUser($tokenStorage->getToken()->getUser());
        $form = $handler->buildForm(
            CommentType::class,
            $comment
        );
        if($form->process()) {
            $this->addFlashMessage('success', 'comment.create.successful');
            return $this->redirectToRoute('place_view', [ 'place' => $place->getId() ]);
        }
        $this->addFlashMessage('error', 'place.edit.error');
        return $this->redirectToRoute('place_view', [ 'place' => $place->getId() ]);
    }

    /**
     * @Route("/{place}/delete/{comment}", name="comment_delete")
     * @Method("GET")
     * @param Comment $comment
     * @param Place $place
     * @param CommentManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Place $place, Comment $comment, CommentManager $manager)
    {
        $manager->remove($comment);
        $this->addFlashMessage('success', 'comment.delete.successful');

        return $this->redirectToRoute('place_view', [ 'place' => $place->getId() ]);
    }


}