<?php

namespace Yarsha\MainBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Yarsha\MainBundle\Entity\Location;

class LocationRepository extends EntityRepository
{

    public function getLocationListQueryBuilder($filters = [])
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('l')
            ->from(Location::class, 'l')
            ->where('l.deleted = 0')
        ;

        if (array_key_exists('id', $filters) and $filters['id'] != '') {
            $qb->andWhere('l.country = :country')->setParameter('country', $filters['id']);
        }

        if (array_key_exists('name', $filters) and $filters['name'] != '') {
            $qb->andWhere(
                $qb->expr()->like('l.name', $qb->expr()->literal("%" . $filters['name'] . "%"))
            );
        }


        return $qb;
    }

    public function getLocationListByCountryId($filters = [])
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('l')
            ->from(Location::class, 'l')
            ->where('l.deleted = 0')
        ;

        if (array_key_exists('id', $filters) and $filters['id'] != "") {
            $qb->andWhere('l . country = :country')->setParameter('country', $filters['id']);
        }

        return $qb;
    }

    public function getLocationsForSelect($filters)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(['l.id', 'l.name'])
            ->from(Location::class, 'l')
            ->where('l.deleted = 0')
        ;

        if (array_key_exists('country', $filters) and $filters['country'] != "") {
            $qb->andWhere('l.country = :country')->setParameter('country', $filters['country']);
        }

        $qb->orderBy('l.name', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }

}
