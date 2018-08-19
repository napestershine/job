<?php

namespace Yarsha\JobSeekerBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Yarsha\JobSeekerBundle\Entity\EmployeeAppliedJob;

/**
 * Class EmployeeAppliedJobRepository
 * @package Yarsha\JobSeekerBundle\Repository
 */
class EmployeeAppliedJobRepository extends EntityRepository
{

    public function getApplicantsListQueryBuilder($filters = [])
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('a')
            ->from(EmployeeAppliedJob::class, 'a')
            ->join('a.job', 'j')
            ->join('j.organization', 'o');

        if (array_key_exists('organization', $filters) and $filters['organization'] != "") {
            $qb->andWhere('o.id = :organization')->setParameter('organization', $filters['organization']);
        }

        if (array_key_exists('job', $filters) and $filters['job'] != "") {
            $qb->andWhere('j.id = :job')->setParameter('job', $filters['job']);
        }

//        $qb->groupBy('a.employee');

        return $qb;
    }

    public function getAppliedJobsBySeekerListQueryBuilder($seeker, $filters = [])
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('a')
            ->from(EmployeeAppliedJob::class, 'a')
            ->where('a.employee = :seeker')->setParameter('seeker', $seeker->getId())
            ->orderBy('a.id', 'DESC');

        return $qb;
    }

    public function getLatestJobsAppliedbySeeker($seeker, $count = null)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('a')
            ->from(EmployeeAppliedJob::class, 'a')
            ->where('a.employee = :seeker')->setParameter('seeker', $seeker->getId());
        $qb->orderBy('a.createdDate', 'desc');

        if ($count != null and $count > 0) {
            $qb->setMaxResults($count);
        }

        return $qb->getQuery()->getResult();
    }

    public function getLatestJobsAppliedbySeekerEmployer($org, $count = null)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('a')
            ->from(EmployeeAppliedJob::class, 'a')
            ->join('a.job', 'j')
            ->where('j.organization = :org')->setParameter('org', $org->getId());
        $qb->orderBy('a.createdDate', 'desc');

        if ($count != null and $count > 0) {
            $qb->setMaxResults($count);
        }

        return $qb->getQuery()->getResult();
    }


    public function getJobsApplied($job)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('a')
            ->from(EmployeeAppliedJob::class, 'a')
            ->where('a.job = :job')->setParameter('job', $job);
        $qb->orderBy('a.createdDate', 'desc');


        return $qb->getQuery()->getResult();
    }


}
