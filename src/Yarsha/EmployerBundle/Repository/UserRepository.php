<?php
/**
 * Created by PhpStorm.
 * User: yarsha
 * Date: 2/16/17
 * Time: 11:32 AM
 */

namespace Yarsha\EmployerBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\JobSeekerBundle\Entity\EmployeeAppliedJob;
use Yarsha\JobsBundle\JobConstants;
use Yarsha\EmployerBundle\Entity\User as Employer;
use Yarsha\JobSeekerBundle\Entity\JobSeekerSetting;
use Yarsha\OrganizationBundle\Entity\Organization;
use Yarsha\JobSeekerBundle\Entity\User as Seeker;

class UserRepository extends EntityRepository
{

    public function countEmployersByStatus()
    {
        $manager = $this->getEntityManager();
        $qb = $manager->createQuery(
            "
            SELECT SUM(CASE WHEN e.enabled = TRUE THEN 1 ELSE 0 END) AS enabled_employers,
                    SUM(CASE WHEN e.enabled = FALSE THEN 1 ELSE 0 END) AS disabled_employers,
                    SUM(CASE WHEN e.id != '' THEN 1 ELSE 0 END) AS total_employers
                    FROM YarshaEmployerBundle:User  e
            "
        );

        return $qb->getArrayResult();
    }

    public function getTotalApplicantsByOrganization($organization)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('COUNT(applied.id)')
            ->from(EmployeeAppliedJob::class, 'applied')
            ->join('applied.job', 'j')
            ->join('j.organization', 'o')
            ->join('applied.employee', 'e')
            ->where('o.id = :organization')->setParameter('organization', $organization)
            ->andWhere('e.enabled = :enabled')->setParameter('enabled', true)
            ->andWhere('j.status = :status')->setParameter('status', JobConstants::JOB_STATUS_APPROVED);

        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (\Exception $e) {
            return 0;
        }

    }

    public function getTotalApplicantByEmployer($id)
    {

        $qb = $this->_em->createQueryBuilder();
        $qb->select('applied')
            ->from(EmployeeAppliedJob::class, 'applied')
            ->join('applied.job', 'j')
            ->join('applied.employee', 'e')
            ->where('j.userId =' . $id)
            ->andWhere('j.jobsFrom = :jobsFrom')->setParameter('jobsFrom', JobConstants::JOB_FROM_EMPLOYERS)
            ->andWhere('e.enabled = :enabled')->setParameter('enabled', true)
            ->andWhere('j.status = :status')->setParameter('status', JobConstants::JOB_STATUS_APPROVED);

        return $qb;
    }


    public function getResumeFromAppliedJob($id, $filter)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('applied')
            ->from(EmployeeAppliedJob::class, 'applied')
            ->join('applied.job', 'j')
            ->join('j.locations', 'l')
            ->join('applied.employee', 'e')
            ->where('j.userId =' . $id)
            ->andWhere('j.jobsFrom = :jobsFrom')->setParameter('jobsFrom', JobConstants::JOB_FROM_EMPLOYERS)
            ->andWhere('e.enabled = :enabled')->setParameter('enabled', true)
            ->andWhere('j.status = :status')->setParameter('status', JobConstants::JOB_STATUS_APPROVED);


        if (array_key_exists('type', $filter) and $filter['type'] != "") {
            $qb->andWhere(
                $qb->expr()->like('applied.status', $qb->expr()->literal("%" . $filter['type'] . "%"))
            );
        }

        if (isset($filter['category']) && $filter['category'] != '') {
            $qb->andWhere('j.category = :category')->setParameter('category', $filter['category']);
        }
        if (isset($filter['industry']) && $filter['industry'] != '') {
            $qb->andWhere('j.industry = :industry')->setParameter('industry', $filter['industry']);
        }

        if (isset($filter['gender']) && $filter['gender'] != '') {
            $qb->andWhere('e.gender = :gender')->setParameter('gender', $filter['gender']);
        }
        if (isset($filter['location']) && $filter['location'] != '') {
            $qb->andWhere('l.id = :location')->setParameter('location', $filter['location']);
        }
        if (isset($filter['education']) && $filter['education'] != '') {
            $qb->andWhere('e.degree = :education')->setParameter('education',
                $filter['education']);
        }

        if (isset($filter['marital_status']) && $filter['marital_status'] != '') {
            $qb->andWhere('e.maritalStatus = :maritalStatus')->setParameter('maritalStatus',
                $filter['marital_status']);
        }


        if (isset($filter['year']) && $filter['year'] != '') {
            $yr = explode('-', $filter['year']);
            $qb->andWhere('e.noOfYear >=' . $yr[0]);
            $qb->andWhere('e.noOfYear <=' . $yr[1]);
        }

        return $qb;
    }


    public function getSearchResume($id, $filter)
    {

        $qb = $this->_em->createQueryBuilder();
        $qb->select('e')
            ->from(Seeker::class, 'e')
            ->leftJoin(JobSeekerSetting::class, 's', 'WITH', 'e.id = s.jobSeeker')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->eq('s.profile_searchable', 1),
                    $qb->expr()->isNull('s.profile_searchable')
                )
            )
            ->andWhere('e.enabled = :enabled')->setParameter('enabled', true)
            ->andWhere('e.deleted = :deleted')->setParameter('deleted', false);

        if (isset($filter['category']) && $filter['category'] != '') {
            $qb->join('e.preferredCategories', 'c');
            $qb->andWhere('c.id = :category')->setParameter('category', $filter['category']);
        }
        if (isset($filter['industry']) && $filter['industry'] != '') {
            $qb->join('e.preferredIndustries', 'i');
            $qb->andWhere('i.id = :industry')->setParameter('industry', $filter['industry']);
        }

        if (isset($filter['gender']) && $filter['gender'] != '') {
            $qb->andWhere('e.gender = :gender')->setParameter('gender', $filter['gender']);
        }
        if (isset($filter['location']) && $filter['location'] != '') {
            $qb->join('e.preferredLocations', 'l');
            $qb->andWhere('l.id = :location')->setParameter('location', $filter['location']);
        }
        if (isset($filter['education']) && $filter['education'] != '') {
            $qb->andWhere('e.degree = :education')->setParameter('education',
                $filter['education']);
        }

        if (isset($filter['marital_status']) && $filter['marital_status'] != '') {
            $qb->andWhere('e.maritalStatus = :maritalStatus')->setParameter('maritalStatus',
                $filter['marital_status']);
        }
        if (isset($filter['type']) && $filter['type'] != '') {
            $qb->andWhere('applied.status = :type')->setParameter('type',
                $filter['type']);
        }

        if (isset($filter['year']) && $filter['year'] != '') {
            $yr = explode('-', $filter['year']);
            $qb->andWhere('e.noOfYear >=' . $yr[0]);
            $qb->andWhere('e.noOfYear <=' . $yr[1]);
        }

        return $qb;
    }


    public function getTotalJobPosted($id)
    {

        $qb = $this->_em->createQueryBuilder();
        $qb->select('j')
            ->from(Job::class, 'j')
            ->where('j.userId =' . $id)
            ->andWhere('j.jobsFrom = :jobsFrom')->setParameter('jobsFrom', JobConstants::JOB_FROM_EMPLOYERS)
            ->andWhere('j.status != :status')->setParameter('status', JobConstants::JOB_STATUS_DELETED);

        return $qb->getQuery()->getResult();
    }

    public function getTotalFollowerByEmployer($empId)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e')
            ->from(Employer::class, 'e')
            ->join('e.followers', 'f')
            ->where('f.id = :employerId')->setParameter('employerId', $empId);

        return $qb->getQuery()->getResult();
    }

    public function getEmployersFollowedBySeeker($seeker)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('o')
            ->from(Organization::class, 'o')
            ->join('o.followers', 'f')
            ->where('f.id = :seeker')->setParameter('seeker', $seeker);

        return $qb->getQuery()->getResult();
    }

    public function related_company($cat, $empId)
    {

        $qb = $this->_em->createQueryBuilder();
        $qb->select('e')
            ->from(Employer::class, 'e')
            ->join('e.organization', 'o')
            ->where('e.enabled = :enabled')->setParameter('enabled', true)
            ->andWhere('e.id != :empID')->setParameter('empID', $empId)
            ->andWhere('o.category = :cat')->setParameter('cat', $cat)
            ->andWhere('o.isNewspaperOrganization = :isNewspaperOrganization')->setParameter('isNewspaperOrganization',
                false)
            ->andWhere('o.isGovermentOrganization = :isGovermentOrganization')->setParameter('isGovermentOrganization',
                false);

        $qb->setMaxResults(10);

        return $qb->getQuery()->getResult();

    }


    public function related_company_jobs($cat, $orgId, $limit = null)
    {
        $currentDate = new \DateTime();
        $qb = $this->_em->createQueryBuilder();
        $qb->select('j')
            ->from(job::class, 'j')
            ->join('j.organization', 'o')
            ->where('j.organization != :orgId')->setParameter('orgId', $orgId)
            ->andWhere('o.category = :cat')->setParameter('cat', $cat)
            ->andWhere('j.jobsFrom = :from')->setParameter('from', JobConstants::JOB_FROM_EMPLOYERS)
            ->andWhere('j.status = :status')->setParameter('status',
                JobConstants::JOB_STATUS_APPROVED)
            ->
            andWhere('j.deadline >= :currentDate')->setParameter('currentDate', $currentDate->format('Y-m-d'))
            ->orderBy('j.createdDate', 'DESC');

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }


    public function getJobsbyEmployer($id, $limit = null)
    {

        $currentDate = new \DateTime();
        $qb = $this->_em->createQueryBuilder();

        $qb->select('j')
            ->from(Job::class, 'j')
            ->where('j.status =' . JobConstants::JOB_STATUS_APPROVED)
            ->andWhere('j.userId =:id')->setParameter('id', $id)
            ->andWhere('j.jobsFrom = :jobsFrom')->setParameter('jobsFrom', JobConstants::JOB_FROM_EMPLOYERS)
            ->andWhere('j.deadline >= :currentDate')->setParameter('currentDate', $currentDate->format('Y-m-d'))
            ->orderBy('j.createdDate', 'DESC');

        if ($limit != null && $limit >= 1) {
            $qb->setMaxResults($limit);
        }

        $qb->orderBy('j.viewCount', 'DESC');

        return $qb->getQuery()->getResult();
    }


}
