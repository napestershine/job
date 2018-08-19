<?php

namespace Yarsha\MainBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Yarsha\MainBundle\Entity\Country;
use Yarsha\MainBundle\Entity\Location;

class CountryRepository extends EntityRepository
{

    public function getCountryListQueryBuilder($filters = [])
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c')
            ->from(Country::class, 'c')
            ->where('c.deleted = 0')
        ;

        if (array_key_exists('name', $filters) and $filters['name'] != '') {
            $qb->andWhere('c.name LIKE :name')->setParameter('name', '%' . $filters['name'] . '%');
        }

        return $qb;
    }


}
