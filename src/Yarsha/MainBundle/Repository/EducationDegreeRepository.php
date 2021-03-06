<?php

namespace Yarsha\MainBundle\Repository;
use Yarsha\MainBundle\Entity\EducationDegree;

/**
 * EducationDegreeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EducationDegreeRepository extends \Doctrine\ORM\EntityRepository
{

    public function getAll($filters = []){
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e')
            ->from(EducationDegree::class, 'e')
            ->where('e.deleted = 0')
            ->orWhere('e.deleted is null')
            ->orderBy('e.sortOrder', 'ASC')
        ;
        return $qb->getQuery()->getResult();
    }

}
