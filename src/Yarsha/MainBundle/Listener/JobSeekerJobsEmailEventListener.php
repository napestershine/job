<?php

namespace Yarsha\MainBundle\Listener;


use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\MainBundle\Service\YarshaMailerService;
use Yarsha\JobSeekerBundle\Entity\User as JobSeeker;
use Yarsha\MainBundle\Event\JobSeekerJobsEmailEvent;

/**
 * Class JobSeekerJobsEmailEventListener
 * @package Yarsha\MainBundle\Listener
 *
 *
 * @DI\Service("yarsha.listener.job_seeker_jobs_email")
 * @DI\Tag(name="kernel.event_listener", attributes={"event" = "yarsha_job_seeker.email_event.job_applied", "method" = "onJobApplied"})
 * @DI\Tag(name="kernel.event_listener", attributes={"event" = "yarsha_job_seeker.email_event.application_shortlisted", "method" = "onApplicationShortlisted"})
 * @DI\Tag(name="kernel.event_listener", attributes={"event" = "yarsha_job_seeker.email_event.application_rejected", "method" = "onApplicationRejected"})
 * @DI\Tag(name="kernel.event_listener", attributes={"event" = "yarsha_job_seeker.email_event.application_selected", "method" = "onApplicationSelected"})
 */
class JobSeekerJobsEmailEventListener
{

    /**
     * @var YarshaMailerService
     */
    private $mailerService;

    /**
     * JobSeekerJobsEmailEventListener constructor.
     * @param YarshaMailerService $mailerService
     *
     * @DI\InjectParams({"mailerService" = @DI\Inject("yarsha.service.mailer")})
     */
    public function __construct(YarshaMailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    public function getProfileStatus(JobSeeker $seeker)
    {
        return $this->mailerService->getSeekerProfileService()->getJobSeekerProfileStatus($seeker);
    }

    public function onJobApplied(JobSeekerJobsEmailEvent $event)
    {
        $data['seeker'] = $seeker = $event->getSeeker();
        $data['profileStatus'] = $this->getProfileStatus($seeker);
        $data['jobs'] = $this->mailerService->getSeekerService()->getMatchedJobBySeeker($seeker, 10);
        $data['job'] = $event->getJob();
        $jobTitle = $event->getJob()->getTitle();
        $data['message'] = "You have applied for job '{$jobTitle}' You may also be interested on the jobs listed below.";
        $subject = 'Application submitted for job "' . $event->getJob()->getTitle() . '.';
        $mailBody = $this->mailerService->getTemplating()
            ->render("@YarshaMain/EmailTemplates/jobseeker_job_status_email.html.twig", $data);
        $this->mailerService->sendEmail($subject, $mailBody, $seeker->getContactEmail());
    }

    public function onApplicationShortlisted(JobSeekerJobsEmailEvent $event)
    {
        $data['seeker'] = $seeker = $event->getSeeker();
        $data['profileStatus'] = $this->getProfileStatus($seeker);
        $data['jobs'] = $this->mailerService->getSeekerService()->getMatchedJobBySeeker($seeker, 10);
        $data['job'] = $event->getJob();
        $organization = $event->getJob()->getOrganization();
        $data['organization'] = $organization;
        $jobTitle = $event->getJob()->getTitle();
        $data['message'] = 'You are shortlisted for  "' .
            $event->getJob()->getTitle() . '" by  organization "' . $organization->getName() . '"';
        $subject = 'You are shortlisted for job "' . $event->getJob()->getTitle() . '.';
        $mailBody = $this->mailerService->getTemplating()
            ->render("@YarshaMain/EmailTemplates/jobseeker_job_status_email.html.twig", $data);
        $this->mailerService->sendEmail($subject, $mailBody, $seeker->getContactEmail());
    }

    public function onApplicationRejected(JobSeekerJobsEmailEvent $event)
    {
        $data['seeker'] = $seeker = $event->getSeeker();
        $data['profileStatus'] = $this->getProfileStatus($seeker);
        $data['jobs'] = $this->mailerService->getSeekerService()->getMatchedJobBySeeker($seeker, 10);
        $data['job'] = $event->getJob();
        $organization = $event->getJob()->getOrganization();
        $data['organization'] = $organization;
        $data['message'] = 'You are rejected for  "' .
            $event->getJob()->getTitle() . '" by  organization "' . $organization->getName() . '"';
        $subject = 'You are rejected for  job "' . $event->getJob()->getTitle() . '.';
        $mailBody = $this->mailerService->getTemplating()
            ->render("@YarshaMain/EmailTemplates/jobseeker_job_status_email.html.twig", $data);
        $this->mailerService->sendEmail($subject, $mailBody, $seeker->getContactEmail());
    }

    public function onApplicationSelected(JobSeekerJobsEmailEvent $event)
    {
        $data['seeker'] = $seeker = $event->getSeeker();
        $data['profileStatus'] = $this->getProfileStatus($seeker);
        $data['jobs'] = $this->mailerService->getSeekerService()->getMatchedJobBySeeker($seeker, 10);
        $data['job'] = $event->getJob();
        $organization = $event->getJob()->getOrganization();
        $data['organization'] = $organization;
        $data['message'] = 'You are selected for  "' .
            $event->getJob()->getTitle() . '" by  organization "' . $organization->getName() . '"';
        $subject = 'You are selected for  "' .
            $event->getJob()->getTitle() . '.';
        $mailBody = $this->mailerService->getTemplating()
            ->render("@YarshaMain/EmailTemplates/jobseeker_job_status_email.html.twig", $data);
        $this->mailerService->sendEmail($subject, $mailBody, $seeker->getContactEmail());
    }
}
