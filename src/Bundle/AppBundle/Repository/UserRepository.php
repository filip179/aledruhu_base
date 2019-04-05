<?php

namespace AppBundle\Repository;

use AppVerk\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserRepository extends \Doctrine\ORM\EntityRepository {
    public function loadUserByUsername($username)
    {
        $user = $this->findUserByUsername($username);

        if (!$user) {
            $user = $this->findUserByEmail($username);
        }

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Email "%s" does not exist.', $username));
        }

        return $user;
    }

    /**
     * @param $username
     *
     * @return UserInterface
     */
    public function findUserByUsername($username)
    {
        /** @var UserInterface $user */
        $user = $this->findOneBy(
            [
                'username' => $username,
            ]
        );

        return $user;
    }

    /**
     * @param $email
     *
     * @return UserInterface
     */
    public function findUserByEmail($email)
    {
        /** @var UserInterface $user */
        $user = $this->findOneBy(
            [
                'email' => $email,
            ]
        );

        return $user;
    }

    public function getUsersQuery(array $filters = [])
    {
        $query = $this->createQueryBuilder('u');

        $query->join('u.role', 'ro');

        if ($filters) {
            foreach ($filters as $field => $like) {
                if (empty($like)) {
                    continue;
                }

                if ($field == 'translatedRole') {
                    $query->join('u.role', 'r')
                        ->andWhere('r.name = :role')
                        ->setParameter('role', $like);
                    continue;
                }
                $query->andWhere("u.$field LIKE :like")
                    ->setParameters(
                        [
                            'like' => $like . '%',
                        ]
                    );
            }
        }

        return $query;
    }

    public function getUsersCount()
    {
        $query = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)');

        return $query->getQuery()->getSingleScalarResult();
    }
}
