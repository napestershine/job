<?php

namespace Yarsha\JobSeekerBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\JobSeekerBundle\Entity\JobSeekerSetting;
use Yarsha\JobSeekerBundle\Entity\User as JobSeeker;
use Yarsha\JobSeekerBundle\Entity\User;
use Yarsha\JobSeekerBundle\Service\JobSeekerProfileService;

/**
 * Class JobSeekerTwigExtension
 * @package Yarsha\OrganizationBundle\Twig
 *
 * @DI\Service("yarsha.twig_jobseeker")
 * @DI\Tag(name="twig.extension")
 */
class JobSeekerTwigExtension extends \Twig_Extension
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var JobSeekerProfileService
     */
    private $profileService;

    /**
     * @DI\InjectParams({
     *      "em" = @DI\Inject("doctrine.orm.entity_manager"),
     *      "profileService" = @DI\Inject("yarsha.service.jobseeker_profile")
     * })
     */
    public function __construct(EntityManager $em, JobSeekerProfileService $profileService)
    {
        $this->em = $em;

        $this->profileService = $profileService;
    }


    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('bootstrap_alert_message', [$this, 'bootstrapAlertMessage']),
            new \Twig_SimpleFunction('isAlreadyApplied', [$this, 'isAlreadyApplied']),
            new \Twig_SimpleFunction('job_is_expired', [$this, 'isExpired']),
            new \Twig_SimpleFunction('job_seeker_profile_status', [$this, 'getProfileStatus']),
            new \Twig_SimpleFunction('job_seeker_settings', [$this, 'getJobSeekerSettings']),
            new \Twig_SimpleFunction('phone_call', [$this, 'phoneCall'], ['is_safe' => ['html']])

        ];
    }

    public function bootstrapAlertMessage($message, $class = 'success')
    {
        $alertHtml = "<div class='alert alert-$class alert-dismissible' role='alert'>
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                            </button>
                            $message
                    </div >";

        return $alertHtml;

    }

    public function isAlreadyApplied($job, $user)
    {
        $status = $this->em->getRepository('YarshaJobSeekerBundle:EmployeeAppliedJob')->findOneBy([
            'employee' => $user,
            'job' => $job
        ]);

        return $status ? true : false;
    }

    public function isExpired($job)
    {
        return $job->getDeadLine()->format('Y-m-d') < date('Y-m-d') ? true : false;
    }

    public function getJobSeekerSettings(User $seeker)
    {
        return $this->em->getRepository(JobSeekerSetting::class)->findOneBy(['jobSeeker' => $seeker]);
    }

    public function getProfileStatus(JobSeeker $jobSeeker, $returnOverall = true)
    {
        return $this->profileService->getJobSeekerProfileStatus($jobSeeker, $returnOverall);
    }


    public function phoneCall($seekerId)
    {

        $phonecall = $this->em->getRepository('YarshaJobSeekerBundle:JobSeekerCallRecord')->findOneBy([
            'seeker' => $seekerId,
        ]);

        if ($phonecall) {
            $alertHtml = "<a data-toggle=\"modal\" data-target=\"#seekerCallRecord\"
                                       id=\"employeecalls-" . $seekerId . "\"
                                       class=\"btn btn-xs btn-warning\"
                                       data-id=\"" . $seekerId . "\">Called
                                    </a>";

        } else {
            $alertHtml = "<a data-toggle=\"modal\" data-target=\"#seekerCallRecord\"
                                       id=\"employeecalls-" . $seekerId . "\"
                                       class=\"btn btn-xs btn-danger\"
                                       data-id=\"" . $seekerId . "\">Phone Calls
                                    </a>";

        }

        return $alertHtml;

    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'job_seeker_twig';
    }
}
