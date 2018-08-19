<?php

namespace Yarsha\JobSeekerBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Response;
use Yarsha\JobSeekerBundle\Entity\User;
use Yarsha\MainBundle\Service\AbstractService;
use Yarsha\JobSeekerBundle\Entity\User as JobSeeker;
use Yarsha\JobSeekerBundle\Entity\EmployeeJobBasket;
use Yarsha\JobSeekerBundle\Entity\EmployeeAppliedJob;

/**
 * Class JobSeekerService
 * @package Yarsha\JobSeekerBundle\Service
 *
 * @DI\Service("yarsha.service.job_seeker", parent="yarsha.service.abstract")
 * @DI\Tag("yarsha.abstract_service")
 */
class JobSeekerService extends AbstractService
{

    private $seeker;

    private $container;

    /**
     * JobSeekerService constructor.
     * @param JobSeeker $seeker
     * @DI\InjectParams({
     *     "seeker" = @DI\Inject("security.token_storage"),
     *     "container" = @DI\Inject("service_container")
     * })
     */
    public function __construct($seeker, $container)
    {
        $this->seeker = $seeker;
        $this->container = $container;
    }

    public function getSeekerById($id)
    {
        return $this->getEntityManager()->getRepository(User::class)->find($id);
    }

    public function getSeekerByUsername($username)
    {
        return $this->getEntityManager()->getRepository(JobSeeker::class)->findOneBy(['username' => $username]);
    }

    public function saveJobApplied($seeker, $job, $type, $status)
    {
        $jobApplied = new EmployeeAppliedJob();
        $jobApplied->setEmployee($seeker);
        $jobApplied->setJob($job);
        $jobApplied->setType($type);
        $jobApplied->setStatus($status);
        $this->getEntityManager()->persist($jobApplied);
        $this->getEntityManager()->flush();
    }

    public function checkAlreadyApplied($seeker, $job)
    {
        $status = $this->getEntityManager()->getRepository(EmployeeAppliedJob::class)->findOneBy(
            [
                'job' => $job,
                'employee' => $seeker
            ]
        );

        return $status ? true : false;
    }

    public function addToJobBasket($job, $seeker)
    {
        $em = $this->getEntityManager();
        $basket = new EmployeeJobBasket();
        $basket->setEmployee($seeker);
        $basket->setJob($job);
        $em->persist($basket);
        $em->flush();
    }

    public function checkAlreadyAddedToBasket($job, $seeker)
    {
        $alreadyAdded = $this->getEntityManager()->getRepository(EmployeeJobBasket::class)->findOneBy([
            'employee' => $seeker,
            'job' => $job
        ]);

        return $alreadyAdded ? true : false;
    }

    public function getJobBasketJobById($id)
    {
        return $this->getEntityManager()->getRepository(EmployeeJobBasket::class)->find($id);
    }

    public function removeJobFromBasket($job)
    {
        $em = $this->getEntityManager();
        $em->remove($job);
        $em->flush();
    }

    public function cancelJobApplication($job, $seeker)
    {
        $em = $this->getEntityManager();
        $appliedJob = $em->getRepository(EmployeeAppliedJob::class)->findOneBy([
            'job' => $job,
            'employee' => $seeker
        ]);
        $em->remove($appliedJob);
        $em->flush();
    }

    public function getJobsAppliedBySeeker($seeker, $count = null)
    {
        $em = $this->getEntityManager();
        $jobs = $em->getRepository(EmployeeAppliedJob::class)->getLatestJobsAppliedbySeeker($seeker);

        return $jobs;
    }

    public function getJobsAppliedBySeekerLatest($seeker, $count = null)
    {
        $em = $this->getEntityManager();

        return $em->getRepository(EmployeeAppliedJob::class)->getLatestJobsAppliedbySeeker($seeker, $count);

    }


    public function getPaginatedAppliedJobsBySeeker($seeker, $filters = [])
    {
        return $this->getPaginationService(
            $this->getEntityManager()->getRepository(EmployeeAppliedJob::class)->getAppliedJobsBySeekerListQueryBuilder($seeker,
                $filters)
        );
    }

    public function getPaginatedApplicantsList($filters = [])
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(EmployeeAppliedJob::class)->getApplicantsListQueryBuilder($filters)
        );
    }

    public function getSeekerProfile($seeker, $type = '', $paginated = false)
    {
        $seekerRepo = $this->getEntityManager()->getRepository(User::class);
        $qb = $seekerRepo->getJobSeekerProfile($seeker, $type);

        return $paginated ? $this->getPaginationService()->getPagerFanta($qb) : $qb->getQuery()->getResult();
    }

    public function getMatchedJobBySeeker($seeker, $limit = null)
    {
        return $this->getEntityManager()->getRepository(JobSeeker::class)->getMatchedJobsBySeeker($seeker, $limit);
    }

    public function getSeekerFollowedCompanies($id)
    {
        return $this->getEntityManager()->getRepository(JobSeeker::class)->getApplicantFollowedCompanies($id);
    }


    public function getSeekerEducation($seeker)
    {
        $em = $this->getEntityManager();
        $qb = $em->getRepository(JobSeeker::class)->getJobSeekerEducationQueryBuilder($seeker);

        return $qb->getQuery()->getResult();
    }

    public function downloadGeneratedResume($seekerId)
    {
        $session = $this->container->get('session');
        $session->save();
        session_write_close();
        $jobseeker = $this->getRepository(User::class)->find($seekerId);
        $filename = $jobseeker->getFirstName() . '-' . date('Ymd');
        $options = [
            'no-outline' => true,
            'page-size' => 'LETTER',
            'encoding' => 'UTF-8',
        ];
        $html = $this->container->get('templating')
            ->render('@YarshaJobSeeker/pdf/resume_format.html.twig', [
                'detail' => $jobseeker
            ]);

        return new Response(
            $this->container->get('knp_snappy.pdf')->getOutputFromHtml($html, $options),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.pdf"'
            ]
        );
    }

    public function getBirthdaySeekers()
    {
        return $this->getEntityManager()->getRepository(JobSeeker::class)->getTodaysBirthdaySeekers();
    }

    public function getSearcheableSeeker(){

        return $this->getEntityManager()->getRepository(JobSeeker::class)->getAllSearcheableSeeker();

    }

    public function getOtherJobseekers($lug){

        return $this->getEntityManager()->getRepository(JobSeeker::class)->getOtherJobseekers($lug);

    }


}
