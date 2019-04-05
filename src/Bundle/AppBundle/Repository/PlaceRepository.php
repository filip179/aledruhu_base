<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PlaceRepository extends EntityRepository
{

    public function getPlacesForMap($filter = null)
    {
        $query = $this->createQueryBuilder('p');
        if ($filter) {
            $query->andWhere('p.placeType = :place_type_id')
                ->setParameter('place_type_id', $filter);
        }

        $query->andWhere('p.longitude IS NOT NULL AND p.latitude IS NOT NULL');
        return $query->getQuery()->getResult();
    }

    public function getPlacesQuery(array $filters = [])
    {
        $query = $this->createQueryBuilder('p');

        if ($filters) {

            foreach ($filters as $field => $like) {
                if (empty($like)) {
                    continue;
                }
                if ($field == 'placeType') {
                    $query->join('p.placeType', 'pt')
                        ->andWhere('pt.name = :type')
                        ->setParameter('type', $like);
                    continue;
                }
                $query->andWhere('p.' . $field . ' LIKE :like')
                    ->setParameters(
                        [
                            'like' => '%' . $like . '%',
                        ]
                    );
            }
        }

        return $query;
    }

    public function getPlacesCount()
    {
        $query = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)');

        return $query->getQuery()->getSingleScalarResult();
    }
}
