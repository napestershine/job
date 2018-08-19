<?php

namespace Yarsha\MainBundle\Listener;


use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\JobsBundle\JobConstants;
use Yarsha\MainBundle\Event\EmployerJobsEmailEvent;
use Yarsha\MainBundle\Service\YarshaMailerService;
use Yarsha\OrganizationBundle\Service\OrganizationService;

/**
 * Class EmployerJobsEmailEventListener
 * @package Yarsha\MainBundle\Listener
 *
 *
 * @DI\Service("yarsha.listener.employer_jobs_email")
 * @DI\Tag(name="kernel.event_listener", attributes={"event" = "yarsha_employer.email_event.job_posted", "method" = "onJobPosted"})
 * @DI\Tag(name="kernel.event_listener", attributes={"event" = "yarsha_employer.email_event.job_approved", "method" = "onJobApproved"})
 * @DI\Tag(name="kernel.event_listener", attributes={"event" = "yarsha_employer.email_event.job_updated", "method" = "onJobUpdated"})
 * @DI\Tag(name="kernel.event_listener", attributes={"event" = "yarsha_employer.email_event.job_deleted", "method" = "onJobDeleted"})
 * @DI\Tag(name="kernel.event_listener", attributes={"event" = "yarsha_employer.email_event.job_disabled", "method" = "onJobDisabled"})
 */
class EmployerJobsEmailEventListener
{

    /**
     * @var YarshaMailerService
     */
    private $mailerService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * EmployerJobsEmailEventListener constructor.
     * @param YarshaMailerService $mailerService
     * @param OrganizationService $organizationService
     *
     * @DI\InjectParams({
     *     "mailerService" = @DI\Inject("yarsha.service.mailer"),
     *     "organizationService" = @DI\Inject("yarsha.service.organization"),
     *     })
     */
    public function __construct(
        YarshaMailerService $mailerService,
        OrganizationService $organizationService
    ) {
        $this->mailerService = $mailerService;
        $this->organizationService = $organizationService;
    }

    public function getContactEmail($organization)
    {
        $cp = $this->organizationService->getContactPersonDetailsByOrganizationId($organization->getId());

        return $cp ? $cp->getEmail() : '';
    }

    public function onJobPosted(EmployerJobsEmailEvent $event)
    {
        $data['employer'] = $employer = $event->getEmployer();
        $organization = $employer->getOrganization();
        $contactEmail = $this->getContactEmail($organization);
        if ($contactEmail) {
            $data['job'] = $event->getJob();
            $subject = '"' . $event->getJob()->getTitle() . '" posted successfully.';
            $mailBody = $this->mailerService->getTemplating()
                ->render("@YarshaMain/EmailTemplates/Employer/job_posted.html.twig", $data);
            $this->mailerService->sendEmail($subject, $mailBody, $contactEmail);
        }
    }

    public function onJobApproved(EmployerJobsEmailEvent $event)
    {
        $job = $event->getJob();

        if ($job) {
            $organization = $job->getOrganization();
            if ($organization and ($contactEmail = $this->getContactEmail($organization))) {
                $data['organization'] = $organization;
                $data['job'] = $event->getJob();
                $subject = '"' . $event->getJob()->getTitle() . '" is approved.';
                $mailBody = $this->mailerService->getTemplating()
                    ->render("@YarshaMain/EmailTemplates/Employer/job_approved.html.twig", $data);
                $this->mailerService->sendEmail($subject, $mailBody, $contactEmail);
            }
        }
    }

    public function onJobUpdated(EmployerJobsEmailEvent $event)
    {
        $em = $this->mailerService->getEntityManager();
        $job = $event->getJob();
        $job->setStatus(JobConstants::JOB_STATUS_DISABLED);
        $data['employer'] = $employer = $event->getEmployer();
        try {
            $em->flush();
            // $contactEmail = $employer->getOrganization()->getEmail();
            $organization = $employer->getOrganization();
            $contactEmail = $this->getContactEmail($organization);
            if ($contactEmail) {
                $data['job'] = $event->getJob();
                $subject = '"' . $event->getJob()->getTitle() . '" is updated.';
                $mailBody = $this->mailerService->getTemplating()
                    ->render("@YarshaMain/EmailTemplates/Employer/job_updated.html.twig", $data);
                $this->mailerService->sendEmail($subject, $mailBody, $contactEmail);
            }
        } catch (\Exception $e) {
            return;
        }
    }

    public function onJobDeleted(EmployerJobsEmailEvent $event)
    {
        $job = $event->getJob();

        if ($job) {
            $organization = $job->getOrganization();
            if ($organization and ($contactEmail = $this->getContactEmail($organization))) {
                $data['organization'] = $organization;
                $data['job'] = $event->getJob();
                $subject = '"' . $event->getJob()->getTitle() . '" is deleted.';
                $mailBody = $this->mailerService->getTemplating()
                    ->render("@YarshaMain/EmailTemplates/Employer/job_deleted.html.twig", $data);
                $this->mailerService->sendEmail($subject, $mailBody, $contactEmail);
            }
        }

        return;

    }

    public function onJobDisabled(EmployerJobsEmailEvent $event)
    {
        $em = $this->mailerService->getEntityManager();
        $job = $event->getJob();
        $job->setStatus(JobConstants::JOB_STATUS_DISABLED);
        $data['employer'] = $employer = $event->getEmployer();
        try {
            $em->flush();
            // $contactEmail = $employer->getOrganization()->getEmail();
            $organization = $employer->getOrganization();
            $contactEmail = $this->getContactEmail($organization);
            if ($contactEmail) {
                $data['job'] = $event->getJob();
                $subject = '"' . $event->getJob()->getTitle() . '" is disabled.';
                $mailBody = $this->mailerService->getTemplating()
                    ->render("@YarshaMain/EmailTemplates/Employer/job_disabled.html.twig", $data);
                $this->mailerService->sendEmail($subject, $mailBody, $contactEmail);
            }
        } catch (\Exception $e) {
            return;
        }
    }
}
