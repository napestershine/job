<?php

namespace Yarsha\JobsBundle\Service;

use Yarsha\JobsBundle\Entity\Job;
use Yarsha\EmployerBundle\Entity\User;
use Yarsha\JobsBundle\JobConstants;
use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\JobsBundle\Entity\JobApplied;
use Yarsha\MainBundle\Service\AbstractService;
use Yarsha\JobSeekerBundle\Entity\EmployeeAppliedJob;


/**
 * Class JobService
 * @DI\Service("yarsha.service.job", parent="yarsha.service.abstract")
 * @DI\Tag("yarsha.abstract_service")
 */
class JobService extends AbstractService
{

    public function findAlreadyAppliedJob($job, $user)
    {
        $applyJob = $this->getEntityManager()->getRepository(JobApplied::class)->findOneBy([
            'status' => 1,
            'job' => $job->getId(),
            'jobSeeker' => $user->getId()
        ]);

        return $applyJob;

    }

    public function saveApplyJob($job, $user, $type)
    {

        $checkAlreadyApply = $this->findAlreadyAppliedJob($job, $user);
        if ($checkAlreadyApply) {

            return $message = 'Already Applied';
        } else {
            $jobApply = new JobApplied();
            $jobApply->setJobSeeker($user);
            $jobApply->setJob($job);
            $jobApply->setType($type);

            $this->getEntityManager()->persist($jobApply);
            try {
                $this->getEntityManager()->flush();
                $message = 'Successful';
            } catch (\Exception $e) {
                $message = $e->getMessage();
            }

            return $message;
        }
    }


    public function savePostJob($form)
    {

        $user = $this->getUser();
        $jobData = $form->getData();
        $setting = $jobData->getSettings();
        $setting->setJob($jobData);
        $jobData->setSettings($setting);
        $jobData->setUser($user);
        $this->getEntityManager()->persist($jobData);
        $this->getEntityManager()->persist($setting);
        $this->getEntityManager()->flush();

    }


    public function getJobById($id)
    {
        return $this->getEntityManager()->getRepository(Job::class)->find($id);
    }

    public function getEmployerById($id)
    {
        return $this->getEntityManager()->getRepository(User::class)->find($id);
    }


    public function getJobByIdandUser($id, $userID)
    {
        return $this->getEntityManager()->getRepository(Job::class)->findBy(
            [
                'id' => $id,
                'user' => $userID
            ]
        );
    }

    public function getPaginatedJobList($filters = [])
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(Job::class)->getJobList($filters)
        );
    }

    public function getPaginatedJobApplicant($data)
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(Job::class)->getApplicantByJob($data)
        );

    }

    public function getPaginatedSearchResult($data)
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(Job::class)->getJobBySearch($data)
        );

    }

    public function getPaginatedSearchQuery($filters)
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(Job::class)->getJobBySearchQuery($filters)
        );

    }

    public function changeStatus($status, $id)
    {

        $job = $this->getJobById($id);

        if ($status == JobConstants::JOB_STATUS_PENDING) {

            $job->setStatus(JobConstants::JOB_STATUS_APPROVED);

            $this->persist($job);

            $this->flush();

        }
        if ($status == JobConstants::JOB_STATUS_APPROVED) {

            $job->setStatus(JobConstants::JOB_STATUS_DISABLED);

            $this->persist($job);

            $this->flush();
        }

        if ($status == JobConstants::JOB_STATUS_DISABLED) {

            $job->setStatus(JobConstants::JOB_STATUS_APPROVED);

            $this->persist($job);

            $this->flush();
        }

        return $job;
    }

    public function getJobsByCategory($id)
    {
        $em = $this->getEntityManager();
        $category = $em->getRepository("YarshaMainBundle:Category")->find($id);

        return $em->getRepository("YarshaJobsBundle:Job")->findBy([
            'category' => $category
        ]);
    }

    public function listJobs($filters = [])
    {
        $queryBuilder = $this->getEntityManager()->getRepository(Job::class)->listJobsQueryBuilder($filters);

        return $this->getPaginationService()->getPagerFanta($queryBuilder);
    }

    public function getJobBySlug($slug = "")
    {
        return ($slug)
            ? $this->getEntityManager()->getRepository(Job::class)->findOneBy([
                'slug' => $slug
            ])
            : null;
    }

    public function getRelatedJobs($job, $limit = null)
    {
        $relatedJobs = [];

        if ($job) {
            $relatedJobs = $this->getEntityManager()->getRepository(Job::class)->getRelatedJobs($job, $limit);
        }

        return $relatedJobs;
    }

    public function getJobsByOrganization($organization, $limit = null, $job = null)
    {
        $jobs = [];
        if ($organization) {
            $jobs = $this->getEntityManager()->getRepository(Job::class)->getJobsByOrganization($organization, $limit,
                $job);
        }

        return $jobs;
    }

    public function checkJobApplied($user, $job)
    {

        return $applied = $this->getEntityManager()->getRepository(EmployeeAppliedJob::class)->findOneBy(
            [
                'employee' => $user,
                'job' => $job
            ]
        );

    }

    public function getEmployerByOrganization($organization)
    {
        return $this->getEntityManager()->getRepository(User::class)->findOneBy([
            'organization' => $organization
        ]);
    }

    public function getRecentJob()
    {

        return $relatedJobs = $this->getEntityManager()->getRepository(Job::class)->getRecentJob();
    }

}
