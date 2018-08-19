<?php

namespace Yarsha\AdminBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Yarsha\AdminBundle\Entity\User;

class AdminUserRepository extends EntityRepository
{

    public function getAccountManagersQueryBuilder($filters)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.deleted = 0')
            ->andWhere(
                $qb->expr()->like('u.roles', $qb->expr()->literal("%ROLE_ACCOUNT_MANAGER%"))
            )
        ;

        return $qb;
    }

    public function getAccountManagersForSelect()
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select(['u.id', 'u.name', 'u.email', 'u.photo'])
            ->from(User::class, 'u')
            ->where('u.deleted = 0')
            ->andWhere(
                $qb->expr()->like('u.roles', $qb->expr()->literal("%ROLE_ACCOUNT_MANAGER%"))
            )
        ;

        return $qb->getQuery()->getArrayResult();
    }

}