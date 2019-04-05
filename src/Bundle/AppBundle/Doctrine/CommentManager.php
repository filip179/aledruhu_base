<?php

namespace AppBundle\Doctrine;


use AppBundle\Entity\Comment;
use AppBundle\Repository\CommentRepository;
use AppVerk\UserBundle\Component\AbstractManager;

class CommentManager extends AbstractManager
{
    public function getCommentsTree($place)
    {
        return $this->getRepository()->getCommentsTree($place);
    }

    public function update(Comment $comment)
    {
        $this->objectManager->persist($comment);
        $this->objectManager->flush();
    }

    public function remove(Comment $comment)
    {
        $this->objectManager->remove($comment);
        $this->objectManager->flush();
    }

    /**
     * @return CommentRepository
     */
    public function getRepository()
    {
        return $this->objectManager->getRepository($this->className);
    }
}
