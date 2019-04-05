<?php

namespace AppBundle\Repository;

class RoleRepository extends \Doctrine\ORM\EntityRepository {
    public function getRolesQuery(array $filters = [])
    {
        $query = $this->createQueryBuilder('r');

        if ($filters) {
            foreach ($filters as $field => $like) {
                if (empty($like)) {
                    continue;
                }
                $query->andWhere("r.$field LIKE :like")
                    ->setParameters(
                        [
                            'like' => '%' . $like . '%',
                        ]
                    );
            }
        }

        return $query;
    }

    public function getRolesCount()
    {
        $query = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)');

        return $query->getQuery()->getSingleScalarResult();
    }
}
