<?php

namespace Yarsha\JobSeekerBundle\Repository;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\JobsBundle\Entity\JobApplied;
use Yarsha\JobSeekerBundle\Entity\EmployeeAppliedJob;
use Doctrine\ORM\EntityRepository;
use Yarsha\EmployerBundle\Entity\User as employer;
use Yarsha\JobSeekerBundle\Entity\EmployeeJobBasket;
use Yarsha\JobSeekerBundle\Entity\JobSeekerEducation;
use Yarsha\JobSeekerBundle\Entity\JobSeekerExperience;
use Yarsha\JobSeekerBundle\Entity\JobSeekerLanguage;
use Yarsha\JobSeekerBundle\Entity\JobSeekerReference;
use Yarsha\JobSeekerBundle\Entity\JobSeekerSetting;
use Yarsha\JobSeekerBundle\Entity\JobSeekerTraining;
use Yarsha\JobSeekerBundle\Entity\User;
use Yarsha\JobSeekerBundle\JobSeekerConstants;
use Yarsha\OrganizationBundle\Entity\Organization as org;
use Yarsha\OrganizationBundle\Entity\Organization;

class UserRepository extends EntityRepository
{

    public function getAllActiveUsers()
    {
        $manager = $this->getEntityManager();
        $qb = $manager->createQuery(
            'SELECT u FROM YarshaJobSeekerBundle:User u WHERE u.enabled = :enabled'
        )
            ->setParameter('enabled', true);
        $results = $qb->getResult();

        return $results;
    }

    public function getAllActiveUsersForMass()
    {
        $manager = $this->getEntityManager();
        $qb = $manager->createQuery(
            'SELECT u FROM YarshaJobSeekerBundle:User u WHERE u.enabled = :enabled'
        )
            ->setParameter('enabled', true);
        $results = $qb->getArrayResult();

        return $results;
    }

    public function getAllUsers($filters = [])
    {


        $qb = $this->_em->createQueryBuilder();

        $qb->select('u')
            ->from('YarshaJobSeekerBundle:User', 'u')
            ->where('u.deleted is NULL')
            ->orWhere('u.deleted != 1');

        $this->jobSeekerFilters($filters, $qb);

        if (array_key_exists('status', $filters) and $filters['status'] != '') {
            $qb->andWhere('u.enabled = :status')->setParameter('status', $filters['status']);
        }
        $qb->orderBy('u.id', 'DESC');

        return $qb;
    }

    /**
     * @param $filters
     * @param $qb
     */
    public function jobSeekerFilters($filters, $qb)
    {
        if (array_key_exists('name', $filters) and $filters['name'] != '') {
            $name = str_replace(' ', '', $filters['name']);
            $qb->andWhere(
                $qb->expr()->like('u.firstName', $qb->expr()->literal('%' . $name . '%'))
            );
        }

        if (array_key_exists('salary', $filters) and $filters['salary'] != '') {
            $qb->andWhere('u.minExpectedSalary<= :minsalary')->setParameter('minsalary', $filters['salary']);

        }
        if (array_key_exists('email', $filters) and $filters['email'] != '') {
            $qb->andWhere('u.contactEmail= :email')->setParameter('email', $filters['email']);

        }
        if (array_key_exists('gender', $filters) and $filters['gender'] != '') {
            $qb->andWhere('u.gender= :gender')->setParameter('gender', $filters['gender']);

        }
        if (array_key_exists('address', $filters) and $filters['address'] != '') {
            $qb->andWhere(
                $qb->expr()->like('u.currentAddress', $qb->expr()->literal('%' . $filters['address'] . '%'))
            );
        }


        if (isset($filters['education']) && $filters['education'] != '') {

            $qb->andWhere('u.degree = :education')->setParameter('education', $filters['education']);
        }

        if (isset($filters['location']) && $filters['location'] != '') {
            $qb->join('u.preferredLocations', 'l');
            $qb->andWhere('l.id = :location')->setParameter('location', $filters['location']);
        }

        if (isset($filters['category']) && $filters['category'] != '') {
            $qb->join('u.preferredCategories', 'cat');
            $qb->andWhere('cat.id = :category')->setParameter('category', $filters['category']);
        }

        if (isset($filters['industry']) && $filters['industry'] != '') {
            $qb->join('u.preferredIndustries', 'ind');
            $qb->andWhere('ind.id = :industry')->setParameter('industry', $filters['industry']);
        }

        if (isset($filters['age']) && $filters['age'] != '') {
            $d = new \DateTime('today -' . $filters['age'] . 'years');
            $age = $d->format('Y-m-d');
            $qb->andWhere('u.dob <=:year')->setParameter('year', $age);
        }


    }

    public function getUserBySearchText($searchText)
    {
        $manager = $this->getEntityManager();
        $qb = $manager->createQueryBuilder(['u']);
        $qb->select('u')
            ->from("YarshaJobSeekerBundle:User", 'u')
            ->where("u.firstName LIKE :searchText")
            ->orWhere("u.lastName LIKE :searchText")
            ->orWhere("u.email LIKE :searchText")
            ->setParameter("searchText", "%" . $searchText . "%");

        return $qb;
    }

    public function getAllAppliedJobs($id, $filters)
    {


        $qb = $this->_em->createQueryBuilder();

        $qb->select('aj')
            ->from(EmployeeAppliedJob::class, 'aj')
            ->join('aj.employee', 'e')
            ->join('aj.job', 'j')
            ->where('e.id = :id')->setParameter('id', $id);

        if (array_key_exists('job_title', $filters) and $filters['job_title'] != '') {
            $qb->andWhere(
                $qb->expr()->like('j.title', $qb->expr()->literal('%' . $filters['job_title'] . '%'))
            );
        }

        return $qb;

    }


    public function countSeekersByStatus()
    {
        $manager = $this->getEntityManager();
        $qb = $manager->createQuery(
            "
              SELECT SUM(CASE WHEN s.enabled = TRUE THEN 1 ELSE 0 END) AS enabled_seekers,
              SUM(CASE WHEN s.enabled = FALSE THEN 1 ELSE 0 END) AS disabled_seekers,
               SUM(CASE WHEN s.id != '' THEN 1 ELSE 0 END) AS total_seekers
              FROM YarshaJobSeekerBundle:User  s
            "
        );

        return $qb->getArrayResult();
    }

    public function getAllAppliedCompanies($id, $filters)
    {


        $qb = $this->_em->createQueryBuilder();

        $qb->select('aj')
            ->from(EmployeeAppliedJob::class, 'aj')
            ->join('aj.employee', 'e')
            ->join('aj.job', 'j')
            ->leftJoin('j.organization', 'o')
            ->where('e.id = :id')->setParameter('id', $id);

        if (array_key_exists('job_title', $filters) and $filters['job_title'] != '') {
            $qb->andWhere(
                $qb->expr()->like('j.title', $qb->expr()->literal('%' . $filters['job_title'] . '%'))
            );
        }
        if (array_key_exists('company_name', $filters) and $filters['company_name'] != '') {
            $qb->andWhere(
                $qb->expr()->like('o.name', $qb->expr()->literal('%' . $filters['company_name'] . '%'))
            );
        }

        return $qb;

    }

    public function getAllFollowedCompanies($id, $filters)
    {

        $qb = $this->_em->createQueryBuilder();

        $qb->select('e')
            ->from(Employer::class, 'e')
            ->join('e.followers', 'f')
            ->join('e.organization', 'o')
            ->where('f.id = :seekerId')->setParameter('seekerId', $id);

        //>andWhere('e.id = :employerId')->setParameter('employerId', $employer->getId());
        if (array_key_exists('company_name', $filters) and $filters['company_name'] != '') {
            $qb->andWhere(
                $qb->expr()->like('o.name', $qb->expr()->literal('%' . $filters['company_name'] . '%'))
            );
        }

        return $qb;

    }

    public function getAllFollowedOrganization($id, $filters)
    {

        $qb = $this->_em->createQueryBuilder();

        $qb->select('o')
            ->from(org::class, 'o')
            ->join('o.followers', 'f')
            ->where('f.id = :seekerId')->setParameter('seekerId', $id);

        //>andWhere('e.id = :employerId')->setParameter('employerId', $employer->getId());
        if (array_key_exists('company_name', $filters) and $filters['company_name'] != '') {
            $qb->andWhere(
                $qb->expr()->like('o.name', $qb->expr()->literal('%' . $filters['company_name'] . '%'))
            );
        }

        return $qb;

    }


    public function getJobSeekerProfile($seeker, $type = '')
    {
        $seekerId = $seeker->getId();

        if ($type == JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_EDUCATION) {
            return $this->getJobSeekerEducationQueryBuilder($seekerId);
        } elseif ($type == JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_PROFESSIONAL) {
            return $this->getJobSeekerExperienceQuery($seekerId);
        } elseif ($type == JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_TRAINING) {
            return $this->getJobSeekerTrainingQuery($seekerId);
        } elseif ($type == 'jobBasket') {
            return $this->getJobSeekerJobBasketQuery($seekerId);
        } elseif ($type == 'appliedJob') {
            return $this->getJobSeekerAppliedJobsQuery($seekerId);
        } elseif ($type == JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_LANGUAGE) {
            return $this->getJobSeekerLanguagesQuery($seekerId);
        } elseif ($type == JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_REFERENCE) {
            return $this->getJobSeekerReferencesQuery($seekerId);
        } elseif ($type == JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_SETTING) {
            return $this->getJobSeekerSettingQuery($seekerId);
        } else {
            throw new \Exception('Type not matched.');
        }

    }

    public function getJobSeekerSettingQuery($seekerId)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e')
            ->from(JobSeekerSetting::class, 'e')
            ->where('e.jobSeeker = :jobSeeker')->setParameter('jobSeeker', $seekerId);

        return $qb;
    }

    public function getJobSeekerReferencesQuery($seekerId)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e')
            ->from(JobSeekerReference::class, 'e')
            ->where('e.jobSeeker = :jobSeeker')->setParameter('jobSeeker', $seekerId);

        return $qb;
    }


    public function getJobSeekerLanguagesQuery($seekerId)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e')
            ->from(JobSeekerLanguage::class, 'e')
            ->where('e.jobSeeker = :jobSeeker')->setParameter('jobSeeker', $seekerId);

        return $qb;
    }

    public function getJobSeekerAppliedJobsQuery($seekerId)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e')
            ->from(JobApplied::class, 'e')
            ->where('e.jobSeeker = :jobSeeker')->setParameter('jobSeeker', $seekerId);

        return $qb;
    }

    public function getJobSeekerJobBasketQuery($seekerId)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e')
            ->from(EmployeeJobBasket::class, 'e')
            ->where('e.employee = :jobSeeker')->setParameter('jobSeeker', $seekerId);

        return $qb;
    }

    public function getJobSeekerExperienceQuery($seekerId)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e')
            ->from(JobSeekerExperience::class, 'e')
            ->where('e.deleted = 0')
            ->orWhere('e.deleted is null')
            ->andWhere('e.jobSeeker = :jobSeeker')->setParameter('jobSeeker', $seekerId);

        return $qb;
    }

    public function getJobSeekerTrainingQuery($seekerId)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e')
            ->from(JobSeekerTraining::class, 'e')
            ->where('e.jobSeeker = :jobSeeker')
            ->setParameter('jobSeeker', $seekerId)
            ->orWhere('e.deleted is null')
            ->andWhere('e.deleted = 0');

        return $qb;
    }

    public function getJobSeekerEducationQueryBuilder($seekerId)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e')
            ->from(JobSeekerEducation::class, 'e')
            ->join('e.degree', 'd')
            ->where('e.deleted = 0')
            ->andWhere('e.jobSeeker = :jobSeeker')->setParameter('jobSeeker', $seekerId)
            ->
            orderBy('d.sortOrder');

        return $qb;
    }

    public function getMatchedJobsBySeeker($seeker, $limit = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('j')
            ->from(Job::class, 'j')
            ->join('j.category', 'c')
            ->join('j.industry', 'i')
            ->join('j.locations', 'l')
            ->where('j.deadline >= :today')->setParameter('today', date('Y-m-d'));
        if (count($seeker->getPreferredLocationsIdArray()) > 0) {
            $qb->orWhere(':locations MEMBER OF  j.locations')->setParameter('locations',
                implode(', ', $seeker->getPreferredLocationsIdArray()));

        }

        if (count($seeker->getPreferredCategoriesIdArray()) > 0) {
            $qb->orWhere('j.category IN (:categories)')->setParameter('categories',
                implode(', ', $seeker->getPreferredCategoriesIdArray()));
        }

        if (count($seeker->getPreferredIndustriesIdArray()) > 0) {
            $qb->orWhere('j.industry IN (:industries)')->setParameter('industries',
                implode(', ', $seeker->getPreferredIndustriesIdArray()));

        }

        if ($limit != null && $limit > 0) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function getApplicantFollowedCompanies($id)
    {

        $qb = $this->_em->createQueryBuilder();

        $qb->select('e')
            ->from(Employer::class, 'e')
            ->join('e.followers', 'f')
            ->where('f.id = :seekerId')->setParameter('seekerId', $id);

        return $qb->getQuery()->getResult();

    }

    public function getTrashedSeekers($filters = [])
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.deleted = 1');
        $this->jobSeekerFilters($filters, $qb);

        return $qb->getQuery()->getResult();
    }

    public function removeFollowedCompaniesBySeeker($seekerId)
    {
        $sql = "DELETE FROM ys_organization_followers where follower_id = $seekerId";

        $this->_em->getConnection()->query($sql);
    }

    public function removeProfileStatusForSeeker($seeker)
    {
        $seekerId = $seeker->getId();

        $profileStatus = $seeker->getProfileStatus() ? $seeker->getProfileStatus()->getId() : '';

        if ($profileStatus != "") {
            $sql = "UPDATE ys_job_seekers SET profile_status_id = NULL where id = $seekerId";
            $this->_em->getConnection()->query($sql);

            $psql = "DELETE FROM ys_job_seeker_profile_completion WHERE id = $profileStatus";
            $this->_em->getConnection()->query($psql);
        }
    }

    public function getJobSeekersWithJobAlertEmailActivated()
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select([
            'js.id as seekerId',
            'js.firstName',
            'js.middleName',
            'js.lastName',
            'js.contactEmail',
            's.job_alert_table'
        ])
            ->from(User::class, 'js')
            ->leftJoin(JobSeekerSetting::class, 's', Join::WITH, 'js.id = s.jobSeeker')
            ->where('s.job_alert_table = 1');

        return $qb->getQuery()->getScalarResult();
    }

    public function getAllSearcheableSeeker()
    {

        $qb = $this->_em->createQueryBuilder();

        $qb->select('u')
            ->from('YarshaJobSeekerBundle:User', 'u')
            ->where('u.deleted != 1')
            ->andWhere('u.enabled =:enabled')->setParameter('enabled',true)
            ->andWhere('u.isSearchable =:isSearchable')->setParameter('isSearchable',true);

        $qb->orderBy('u.id', 'ASC');
        $qb->setMaxResults(6);

        return $qb->getQuery()->getResult();
    }

    public function getOtherJobseekers($slug)
    {

        $qb = $this->_em->createQueryBuilder();

        $qb->select('u')
            ->from('YarshaJobSeekerBundle:User', 'u')
            ->where('u.deleted != 1')
            ->andWhere('u.enabled =:enabled')->setParameter('enabled',true)
            ->andWhere('u.isSearchable =:isSearchable')->setParameter('isSearchable',true)
            ->andWhere('u.username != :username')->setParameter('username',$slug);
        $qb->orderBy('u.id', 'ASC');
        return $qb->getQuery()->getResult();
    }




}
