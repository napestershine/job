<?php

namespace Yarsha\AgencyBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Yarsha\AgencyBundle\Entity\User;

class AgencyUserRepository extends EntityRepository
{

    public function getAllAgencies($filters = [])
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('u')
            ->from('YarshaAgencyBundle:User', 'u')
            ->orWhere('u.deleted != 1');

        if (array_key_exists('name', $filters) and $filters['name'] != '') {
            $name = str_replace(' ', '', $filters['name']);
            $qb->andWhere(
                $qb->expr()->like('u.name', $qb->expr()->literal('%' . $name . '%'))
            );
        }

        $qb->orderBy('u.id', 'DESC');

        return $qb;
    }





}