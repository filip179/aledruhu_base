<?php

namespace AppBundle\Form\Handler;

use AppBundle\Doctrine\CommentManager;
use AppBundle\Entity\Comment;
use Component\Form\Handler\AbstractFormHandler;

class CommentHandler extends AbstractFormHandler
{
    /** @var CommentManager */
    private $manager;

    public function __construct(CommentManager $commentManager)
    {
        $this->manager = $commentManager;
    }

    protected function success()
    {
        /** @var Comment $comment */
        $comment = $this->form->getData();
        $comment->setParentEntity(Comment::getEntityByKey($comment->getParentEntity()));
        if($comment->getId()) {
            $text = $comment->getText();
            $comment = $this->manager->getRepository()->find($comment->getId());
            $comment->setText($text);
        }
        $this->manager->update($comment);

        return true;
    }
}
