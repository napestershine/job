<?php

namespace Yarsha\MainBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Yarsha\EmployerBundle\Entity\User as EmployerUser;
use Yarsha\JobSeekerBundle\Entity\User as Jobseeker;
use Yarsha\JobSeekerBundle\Service\JobSeekerProfileService;
use Yarsha\JobSeekerBundle\Service\JobSeekerService;
use Yarsha\MainBundle\MainBundleConstants;
use Yarsha\OrganizationBundle\Service\OrganizationService;

/**
 * Class YarshaMailerService
 * @package Yarsha\MainBundle\Service
 *
 * @DI\Service("yarsha.service.mailer", parent="yarsha.service.abstract")
 * @DI\Tag("yarsha.abstract_service")
 */
class YarshaMailerService extends AbstractService
{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var TwigEngine
     */
    private $templating;

    /**
     * @var JobSeekerProfileService
     */
    private $seekerProfileService;

    /**
     * @var JobSeekerService
     */
    private $seekerService;

    /**
     * @var OrganizationService
     */
    private $organizationService;


    /**
     * YarshaMailerService constructor.
     * @param \Swift_Mailer $mailer
     * @param Router $router
     * @param TwigEngine $templating
     * @param JobSeekerProfileService $seekerProfileService
     * @param JobSeekerService $seekerService
     * @param OrganizationService $organizationService
     *
     * @DI\InjectParams({
     *     "mailer" = @DI\Inject("swiftmailer.mailer"),
     *     "router" = @DI\Inject("router"),
     *     "templating" = @DI\Inject("templating"),
     *     "seekerProfileService" = @DI\Inject("yarsha.service.jobseeker_profile"),
     *     "seekerService" = @DI\Inject("yarsha.service.job_seeker"),
     *     "organizationService" = @DI\Inject("yarsha.service.organization")
     * })
     */
    public function __construct(
        \Swift_Mailer $mailer
        ,
        Router $router
        ,
        TwigEngine $templating
        ,
        JobSeekerProfileService $seekerProfileService
        ,
        JobSeekerService $seekerService
        ,
        OrganizationService $organizationService
    ) {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
        $this->seekerProfileService = $seekerProfileService;
        $this->seekerService = $seekerService;
        $this->organizationService = $organizationService;
    }

    public function getSeekerProfileService()
    {
        return $this->seekerProfileService;
    }

    public function getSeekerService()
    {
        return $this->seekerService;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function getTemplating()
    {
        return $this->templating;
    }

    public function sendEmail($subject, $body, $toEmail, $fromEmail = '', $fromName = '', $attachment = [])
    {

        $fromEmail = $fromEmail != "" ? $fromEmail : 'noreply@kantipurjob.com';
        $fromName = $fromName != "" ? $fromName : 'Kantipur JOB';

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setTo($toEmail)
            ->setFrom($fromEmail, $fromName)
            ->setBody($body, 'text/html');
        if (array_key_exists('file', $attachment) and array_key_exists('filename', $attachment)) {
            $message->attach(
                \Swift_Attachment::fromPath($attachment['file'])->setFilename($attachment['filename'])
            );
        }

        return $this->mailer->send($message);

    }

    public function updateConfirmationTokenJobSeeker(Jobseeker $user)
    {
        return $this->updateConfirmationToken($user);
    }

    public function updateConfirmationTokenEmployer(EmployerUser $user)
    {
        return $this->updateConfirmationToken($user);
    }

    public function updateConfirmationToken($user)
    {
        $confirmationToken = base64_encode(sha1(random_bytes(10)));

        $userWithConfirmationToken = $this->getEntityManager()->getRepository(get_class($user))->findOneBy([
            'confirmationToken' => $confirmationToken
        ]);

        if ($userWithConfirmationToken) {
            return $this->updateConfirmationToken($user);
        }

        $user->setConfirmationToken($confirmationToken);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $confirmationToken;
    }

    public function getRegistrationConfirmationUrl($user)
    {
        $routeName = ($user instanceof EmployerUser)
            ? 'yarsha_registration_confirmation_employer'
            : ($user instanceof Jobseeker) ? 'yarsha_registration_confirmation_seeker' : '';

        if ($routeName == "") {
            return "";
        }

        $confirmationToken = $this->updateConfirmationToken($user);

        return $this->router->generate($routeName, ['token' => $confirmationToken],
            UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function sendEmployerRegistrationConfirmationEmail(EmployerUser $user, $toEmail)
    {
        $confirmationToken = $this->updateConfirmationToken($user);

        $data['confirmation_link'] = $this->router->generate('yarsha_registration_confirmation_employer',
            ['token' => $confirmationToken], UrlGeneratorInterface::ABSOLUTE_URL);

        $organization = $user->getOrganization();

        $data['organization_name'] = $organization->getName();

        $data['username'] = $user->getUsername();

        $data['email'] = $this->organizationService->getContactEmail($organization->getId());

        $message = $this->templating->render(
            '@YarshaMain/EmailTemplates/Employer/registration_confirmation.html.twig'
            , $data
        );

        return $this->sendEmail('Confirm your registration.', $message, $toEmail);
    }

    public function sendJobSeekerRegistrationConfirmationEmail(Jobseeker $seeker)
    {
        $confirmationToken = $this->updateConfirmationToken($seeker);

        $data['confirmation_url'] = $this->router->generate('yarsha_registration_confirmation_seeker',
            ['token' => $confirmationToken], UrlGeneratorInterface::ABSOLUTE_URL);
        $data['confirmation_link'] = $this->router->generate('yarsha_registration_confirmation_seeker',
            ['token' => $confirmationToken], UrlGeneratorInterface::ABSOLUTE_URL);

        $data['seeker'] = $seeker;
        $data['username'] = $seeker->getUsername();
        $data['email'] = $seeker->getContactEmail();

        $message = $this->templating->render('@YarshaMain/Emails/seeker_registration_confirmation_link.html.twig',
            $data);

        return $this->sendEmail('Confirm your registration.', $message, $seeker->getContactEmail());
    }

    public function sendAlertEmailToSeeker(Jobseeker $seeker, $type)
    {
        $data['seeker'] = $seeker;
        $data['profileStatus'] = $this->seekerProfileService->getJobSeekerProfileStatus($seeker);

        switch ($type) {
            case MainBundleConstants::SEEKER_EMAIL_FOR_BIRTHDAY_MESSAGE:
                $template = '@YarshaMain/EmailTemplates/birthday_wish.html.twig';
                $subject = "Happy birthday " . $seeker->getFirstName() . " | Birthday massage form Kantipur JOB";
                break;
            case MainBundleConstants::SEEKER_EMAIL_FOR_PROFILE_UPDATE_ALERT:
                $template = '@YarshaMain/EmailTemplates/profile_update_alert.html.twig';
                $subject = 'Profile Update Alert I Your profile is not updated';
                break;
            case MainBundleConstants::SEEKER_EMAIL_FOR_JOB_ALERT:
                $template = '@YarshaMain/EmailTemplates/job_alert.html.twig';
                $subject = 'Job Alert | Your Matched JOBS from Kantipur JOB';
                $data['jobs'] = $this->seekerService->getMatchedJobBySeeker($seeker, 20);
                break;
            default:
                $template = '@YarshaMain/EmailTemplates/common_messages.html.twig';
                $subject = 'Hi From Kantipur JOB';
                $data['message'] = "Hello " . $seeker->getFullName();
        }

        $mailBody = $this->templating->render($template, $data);

        return $this->sendEmail($subject, $mailBody, $seeker->getContactEmail());
    }

}
