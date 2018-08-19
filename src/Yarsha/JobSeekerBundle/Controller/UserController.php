<?php

namespace Yarsha\JobSeekerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Yarsha\JobSeekerBundle\Event\ProfileUpdateEvent;
use Yarsha\JobSeekerBundle\Form\JobSeekerRegistrationType;
use Yarsha\MainBundle\Entity\Country;
use Yarsha\MainBundle\MainBundleEvents;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yarsha\JobSeekerBundle\Form\PasswordResetType;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Yarsha\JobSeekerBundle\Entity\User as Jobseeker;
use Yarsha\MainBundle\Service\ImageService;

/**
 * Class UserController
 * @package Yarsha\JobSeekerBundle\Controller
 *
 * @Route("/")
 */
class UserController extends Controller
{

    private $data = [];

    public function resumeAction(Request $request)
    {
        $form = $this->getUploadResumeForm();
        $form->handleRequest($request);
        $this->uploadCvAction($form);
    }

    /**
     * @param Request $request
     * @Route("/cv/upload", name="yarsha_job_seeker_upload_cv")
     */
    public function uploadCvAction($form)
    {
        $user = $this->getUser();
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $file = $form->get('curriculumVitaeFile')->getData();
                $user->setCurriculumVitaeFile($file);
                $user->uploadCv();
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                try {
                    $em->flush();
                    $eventDispatcher = $this->get('event_dispatcher');
                    $profileUpdateEvent = new ProfileUpdateEvent($user);
                    $eventDispatcher->dispatch(MainBundleEvents::EVENT_JOB_SEEKER_PROFILE_UPDATE, $profileUpdateEvent);
                    $this->addFlash('success', 'Curriculum vitae uploaded.');
                } catch (\Exception $e) {
                    $this->addFlash('errorMessage', "Please upload a valid file. only (pdf / docx) are supported.");
                }
            } else {
                $this->addFlash('errorMessage',
                    "Please upload a valid file. only (pdf / word document) file are supported.");
            }
        }
    }

    public function getUploadResumeForm()
    {
        $user = $this->getUser();
        $form = $this->createFormBuilder($user)
            ->add('curriculumVitaeFile', FileType::class, [
                'data_class' => null,
                'required' => true,
                'label' => 'Upload Curriculum Vitae'
            ])
            ->getForm();

        return $form;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/registration/seeker", name="yarsha_frontend_register_as_seeker")
     */
    public function registerAction(Request $request)
    {
        $userManager = $this->get('yarsha_job_seeker.user_manager');
        $user = $userManager->createUser();

        $em = $this->get('doctrine.orm.entity_manager');

        $options['country'] = null;
        $nepal = $em->getRepository(Country::class)->findOneBy(['iso2' => 'NP']);
        if ($nepal) {
            $options['country'] = $nepal->getId();
        }

        $form = $this->createForm(JobSeekerRegistrationType::class, $user, $options);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            try {

                $user = $form->getData();

                $user->setEnabled(false);
                $user->setPlainPassword($user->getPassword());
                $user->addRole('ROLE_JOB_SEEKER');
                $username = $user->getContactEmail();
                $user->setEmail($username);
                $user->setUsername($username);

                $user->uploadCv();

//                $file = $form['file']->getData();
//                if ($file instanceof UploadedFile) {
//                    $user->upload();
//                }

                $profilePicture = $request->files->get('avatar_file');

                if ($profilePicture) {
                    $file = $profilePicture;
                    $source = $request->get('avatar_src');
                    $data = $request->get('avatar_data');
                    $files['error'] = $file->getError();
                    $files['tmp_name'] = $file->getRealPath();
                    $response = new ImageService(
                        $source ? $source : null,
                        $data ? $data : null,
                        $files ? $files : null
                    );

                    if ($response->success == true) {
                        $user->setPath($response->getFilename() . $response->getExtension());
                    }

                }

                $userManager->updateUser($user);

                $this->getDoctrine()->getManager()->flush();

                $eventDispatcher = $this->get('event_dispatcher');
                $profileUpdateEvent = new ProfileUpdateEvent($user);
                $eventDispatcher->dispatch(MainBundleEvents::EVENT_JOB_SEEKER_PROFILE_UPDATE, $profileUpdateEvent);

                $this->get('yarsha.service.mailer')->sendJobSeekerRegistrationConfirmationEmail($user);

                return $this->redirectToRoute('yarsha_job_seeker_registration_success', ['ref' => $user->getId()]);
            } catch (\Exception $e) {
                $this->data['errorMessage'] = $e->getMessage();
            }
        }

        $this->data['form'] = $form->createView();

        return $this->render('@YarshaJobSeeker/UserBundle/Register/register_form_new.html.twig', $this->data);
    }

    /**
     * @return Response
     * @Route("/seeker/render/email", name="yarsha_seeker_render_email_template")
     */
    public function renderEmailTemplateAction()
    {
        $user = $this->getUser();
        $confirmationToken = "awjdkslgnakulghsdfjgsdiuf";
        $data['profileStatus'] = $user->getProfileStatus();
        $data['confirmation_link'] = $this->get('router')->generate('yarsha_registration_confirmation_seeker',
            ['token' => $confirmationToken], UrlGeneratorInterface::ABSOLUTE_URL);
        $data['confirmationToken'] = "awjdkslgnakulghsdfjgsdiuf";
        $data['seeker'] = $user;
        $data['username'] = $user->getUsername();
        $data['email'] = $user->getContactEmail();

        $message = $this->renderView('@YarshaMain/EmailTemplates/job_seeker_registration.html.twig', $data);
        try {
            $this->get('yarsha.service.mailer')->sendEmail('Confirm your registration.', $message,
                '');
        } catch (\Exception $e) {
            dump($e->getMessage());
            exit;
        }

        return new Response($message);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/registration/seeker/success", name="yarsha_job_seeker_registration_success")
     */
    public function registrationSuccessAction(Request $request)
    {
        $data['email'] = ($ref = $request->get('ref') and $seeker = $this->get('doctrine.orm.entity_manager')->find(Jobseeker::class,
                $ref))
            ? $seeker->getContactEmail()
            : '';

        return $this->render("@YarshaJobSeeker/UserBundle/Register/registration_success.html.twig", $data);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @Route("registration/seeker/confirmation/{token}", name="yarsha_registration_confirmation_seeker")
     */
    public function confirmRegistrationAction(Request $request)
    {
        $token = $request->get('token');

        if (!$token) {
            throw new NotFoundHttpException;
        }

        $em = $this->get('doctrine.orm.entity_manager');

        $user = $em->getRepository(Jobseeker::class)->findOneBy(['confirmationToken' => $token]);

        if (!$user) {
            throw new NotFoundHttpException;
        }

        if ($user) {
            try {
                $user->setEnabled(true);

                $user->setConfirmationToken(null);

                $em->persist($user);

                $em->flush();

                $this->addFlash('success', 'Your account has been activated. Please Login.');

                return $this->redirectToRoute('yarsha_job_seeker_security_login');

            } catch (\Exception $e) {

            }
        }

        return $this->render('@YarshaJobSeeker/UserBundle/Register/registration_activation_failure.html.twig', []);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/seeker-registration/confirmed", name="yarsha_job_seeker_registration_confirmation")
     */
    public function registrationConfirmedAction(Request $request)
    {
        $this->data['message'] = 'Registration completed successfully';

        return $this->render('@YarshaJobSeeker/UserBundle/Register/register_confirmed.html.twig', $this->data);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/seeker/changePassword", name="yarsha_job_seeker_change_password")
     * @Breadcrumb("Reset Password")
     */
    public function changePasswordAction(Request $request)
    {
        $this->resumeAction($request);
        $user = $this->getUser();
        $form = $this->createFormBuilder()
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password and confirm password field must match.',
                'options' => [
                    'attr' => [
                        'class' => 'password-field'
                    ]
                ],
                'required' => true,
                'first_options' => ['label' => 'New Password'],
                'second_options' => ['label' => 'Repeat Password']
            ])
            ->add('change', SubmitType::class, [
                'attr' => [
                    'value' => 'Change',
                    'class' => 'btn btn-primary'
                ]
            ])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userData = $form->getData();
            $user->setPlainPassword($userData['plainPassword']);
            $userManager = $this->get('yarsha_job_seeker.user_manager');
            try {
                $userManager->updateUser($user);
                $this->addFlash('success', 'Password Changed. User new password in next login.');
            } catch (\Exception $e) {
                $this->data['errorMessage'] = $e->getMessage();
            }
        }
        $this->data['form'] = $form->createView();

        return $this->render('@YarshaJobSeeker/Home/changepassword.html.twig', $this->data);

    }

    /**
     * @Route("seeker/profileStatus", name="yarsha_job_seeker_profile_status")
     */
    public function profileCompletionStatusAction()
    {
        $seeker = $this->getUser();
        $seekerService = $this->get('yarsha.service.jobseeker_profile');
        dump($seeker);
        dump($seekerService->getProfileCompletionStatus($seeker));
        exit;
    }


    /**
     * @Route("seeker/resetting-request", name="yarsha_job_seeker_resetting-request")
     */

    public function requestAction()
    {

        return $this->render('@YarshaJobSeeker/UserBundle/Resetting/request.html.twig');
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/email-send", name="yarsha_job_seeker_email-send")
     */
    public function sendSeekerEmailAction(Request $request)
    {

        $username = $request->request->get('username');

        /** @var $user UserInterface */
        $user = $this->get('yarsha_job_seeker.user_manager')->findUserByUsernameOrEmail($username);

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        /* Dispatch init event */
        $event = new GetResponseNullableUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        // $ttl = $this->container->getParameter('fos_user.resetting.retry_ttl');

        if ($user != null) {
            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_REQUEST, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            if (null === $user->getConfirmationToken() or $user->getConfirmationToken() == '') {
                /** @var $tokenGenerator TokenGeneratorInterface */
                $tokenGenerator = $this->get('fos_user.util.token_generator');
                $token = $tokenGenerator->generateToken();
                $user->setConfirmationToken($token);
            } else {
                $token = $user->getConfirmationToken();
            }

            /* Dispatch confirm event */
            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_CONFIRM, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

//            $this->get('fos_user.mailer')->sendResettingEmailMessage($user);

            $to = $user->getContactEmail();
            $fullname = $user->getfirstName();

            $hostname = 'http://' . $request->getHttpHost();

            $message = \Swift_Message::newInstance()
                ->setSubject('Password Resetting')
                ->setFrom('noreply@kantipurjob.com', 'Kantipur Job')
                ->setTo($to)
                ->setBody(
                    $this->renderView(
                        'Emails/password_resetting.html.twig',
                        [
                            'name' => $fullname,
                            'host' => $hostname,
                            'token' => $token
                        ]
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);
//            $body = $this->renderView(
//                'Emails/password_resetting.html.twig',
//                [
//                    'name' => $fullname,
//                    'host' => $hostname,
//                    'token' => $token
//                ]
//            );
//            $from = 'noreply@gmail.com';
//            $this->get('yarsha.service.mailer')->sendEmail('Password Resetting', $body, $to, $from);


            $user->setPasswordRequestedAt(new \DateTime());
            $this->get('yarsha_job_seeker.user_manager')->updateUser($user);

            /* Dispatch completed event */
            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_COMPLETED, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }
        } else {

            $this->addFlash('error', 'Invalid username/email.');

            return $this->redirectToRoute('yarsha_job_seeker_resetting-request');

        }

        return new RedirectResponse($this->generateUrl('yarsha_job_seeker_resetting-check-email',
            ['username' => $username]));
    }

    /**
     * Tell the user to check his email provider.
     *
     * @param Request $request
     * @Route("/email-check", name="yarsha_job_seeker_resetting-check-email")
     * @return Response
     */
    public function checkSeekerEmailAction(Request $request)
    {
        $username = $request->query->get('username');

        if (empty($username)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->generateUrl('yarsha_job_seeker_resetting-request'));
        }

        return $this->render('@YarshaJobSeeker/UserBundle/Resetting/email-check.html.twig');

    }


    /**
     * Reset user password.
     *
     * @param Request $request
     * @param string $token
     * @Route("/{token}/password-reset", name="yarsha_job_seeker_password-resetting")
     * @return Response
     */
    public function resetAction(Request $request, $token)
    {

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('yarsha_job_seeker.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"',
                $token));
        }


        $form = $this->createForm(PasswordResetType::class, $user);
        //        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);
            $user->setConfirmationToken(null);
            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('yarsha_job_seeker_homepage');
                $response = new RedirectResponse($url);
            }


            $dispatcher->dispatch(
                FOSUserEvents::RESETTING_RESET_COMPLETED,
                new FilterUserResponseEvent($user, $request, $response)
            );

            return $this->redirectToRoute('yarsha_job_seeker_homepage');
        }

        return $this->render('@YarshaJobSeeker/UserBundle/Resetting/reset.html.twig', [
            'token' => $token,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @return JsonResponse
     * @Route("/send/testemail")
     */
    public function sendTestEmail()
    {
        $mailerService = $this->get('yarsha.service.mailer');
        $subject = "Test Email";
        $body = $this->renderView('@YarshaMain/EmailTemplates/email_notification_layout.html.twig');
        $toEmail = "mandip810@gmail.com";
        try {
            $mailerService->sendEmail($subject, $body, $toEmail);
            $response['success'] = true;
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        return new JsonResponse($response);
    }


}
