<?php

namespace Yarsha\AgencyBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Yarsha\AgencyBundle\Entity\AgencyJob;
use Yarsha\AgencyBundle\Entity\User;

class AgencyJobRepository extends EntityRepository
{

    public function getAllAgenciesJobs($filters = [])
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('j')
            ->from(AgencyJob::class, 'j')
            ->orWhere('j.deleted != 1');

        if (array_key_exists('job_title', $filters) and $filters['job_title'] != '') {
            $name = str_replace(' ', '', $filters['job_title']);
            $qb->andWhere(
                $qb->expr()->like('j.jobTitle', $qb->expr()->literal('%' . $name . '%'))
            );
        }

        if (array_key_exists('agency', $filters) and $filters['agency'] != "") {
            $qb->andWhere('IDENTITY(j.agency) = :agency')
                ->setParameter('agency', $filters['agency']);
        }

        $qb->orderBy('j.id', 'DESC');

        return $qb;
    }

}