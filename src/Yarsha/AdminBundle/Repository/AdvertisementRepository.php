<?php

namespace Yarsha\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Yarsha\AdminBundle\Entity\Advertisement;

class AdvertisementRepository extends EntityRepository
{

    public  function  getAdvertisementsListQueryBuilder($filters = [])
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('a')
            ->from(Advertisement::class, 'a')
            ->where('a.deleted = 0')
        ;

        if( array_key_exists('section', $filters) and $filters['section'] != "" )
        {
            $qb->andWhere('a.section = :section')->setParameter('section', $filters['section']);
        }

        if( array_key_exists('status', $filters) and $filters['status'] != "" )
        {
            $status = ($filters['status'] === true or $filters['status'] == 'Y') ? '1' : '0';
            $qb->andWhere('a.status = :status')->setParameter('status', $status);
        }

        $qb->orderBy('a.id', 'desc');

        return $qb;
    }

    public function getAdvertisementsForFrontend($section = null, $limit = null){
        $qb = $this->_em->createQueryBuilder();
        $qb->select('a')
            ->from(Advertisement::class, 'a')
            ->where('a.deleted = 0')
            ->andWhere('a.status = 1')
        ;

        if($section != null){
            $qb->andWhere('a.section = :section')->setParameter('section', $section);
        }

        if($limit != null && $limit > 0){
            $qb->setMaxResults($limit);
        }


        return $qb->getQuery()->getResult();
    }

}