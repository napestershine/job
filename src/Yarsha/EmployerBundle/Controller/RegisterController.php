<?php

namespace Yarsha\EmployerBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Yarsha\EmployerBundle\Entity\User;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Yarsha\EmployerBundle\Form\PasswordResetType;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Yarsha\OrganizationBundle\OrganizationConstants;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Yarsha\EmployerBundle\Form\EmployerLoginCredentialType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Yarsha\OrganizationBundle\Entity\OrganizationContactPerson;
use Yarsha\EmployerBundle\Form\EmployerRegisterOrganizationType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yarsha\OrganizationBundle\Form\OrganizationContactPersonType;

/**
 * Class RegisterController
 * @package Yarsha\EmployerBundle\Controller
 *
 * @Route("/")
 */
class RegisterController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/registration/employer", name="yarsha_frontend_register_employer")
     */
    public function newAccountAction(Request $request)
    {
        $data = [];

        $form = $this->buildEmployerRegisterForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $formData = $form->getData();

            try {

                $organization = $formData['organization'];
                $organizationContactPerson = $formData['organization_contact_person'];

                $logo = $organization->getFile();
                if ($logo) {
                    $organization->setFile($logo);
                    $organization->upload();
                }
                $organization->setStatus(OrganizationConstants::ORGANIZATION_STATUS_PENDING);
                $organization->setEmail($organizationContactPerson->getEmail());

                $em = $this->get('doctrine.orm.entity_manager');

                $em->persist($organization);

                $organizationContactPerson->setOrganization($organization);
                $em->persist($organizationContactPerson);

                $userDiscriminator = $this->get('rollerworks_multi_user.user_discriminator');
                $userDiscriminator->setCurrentUser('yarsha_employer');

                $userManager = $this->get('yarsha_employer.user_manager');
                $employer = $formData['user_login_credential'];

                $username = $employer->getUsername();
                $password = $employer->getPassword();
//                $email = (strpos($username, '@')) ? $username : $username . '@kantipurjob.com';

                $employer->setUsername($username);
                $employer->setEmail($username);
                $employer->setPlainPassword($password);
                $employer->setEnabled(false);
                $employer->addRole('ROLE_EMPLOYER');
                $employer->setOrganization($organization);

                $userManager->updateUser($employer);

                $em->flush();

//                $mailer = $this->get('yarsha.service.mailer');
//                $to = $organizationContactPerson->getEmail();
//                $mailer->sendEmployerRegistrationConfirmationEmail($employer, $to);

                return $this->redirectToRoute('yarsha_employer_registration_success',
                    ['ref' => $organizationContactPerson->getId()]);

            } catch (\Exception $e) {
                $data['errorMessage'] = $e->getMessage();
            }
        }

        $data['form'] = $form->createView();

        return $this->render('YarshaEmployerBundle:Registration:registration_new.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/registration/employer/success", name="yarsha_employer_registration_success")
     */
    public function registrationSuccessAction(Request $request)
    {
        $data['email'] = ($ref = $request->get('ref') and $cp = $this->get('doctrine.orm.entity_manager')->find(OrganizationContactPerson::class,
                $ref))
            ? $cp->getEmail()
            : '';

        return $this->render("@YarshaEmployer/Registration/registration_success.html.twig", $data);
    }

    private function buildEmployerRegisterForm()
    {
        $form = $this->createFormBuilder()
            ->add('organization', EmployerRegisterOrganizationType::class, [
                'label_attr' => [
                    'class' => 'hidden'
                ]
            ])
            ->add('organization_contact_person', OrganizationContactPersonType::class, [
                'contact_type' => OrganizationContactPerson::CONTACT_TYPE_MAIN_CONTACT,
                'label_attr' => [
                    'class' => 'hidden'
                ]
            ])
            ->add('user_login_credential', EmployerLoginCredentialType::class, [
                'label_attr' => [
                    'class' => 'hidden'
                ]
            ])
            ->getForm();

        return $form;
    }

    /**
     * @Route("employer/resetting-request", name="yarsha_employer_resetting-request")
     */

    public function requestAction()
    {

        return $this->render('@YarshaEmployer/UserBundle/Resetting/request.html.twig');
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/email-send/employer", name="yarsha_employer_email-send")
     */
    public function sendEmployerEmailAction(Request $request)
    {

        $em = $this->get('doctrine.orm.entity_manager');

        $username = $request->request->get('username');

        /** @var $user UserInterface */
        $user = $this->get('yarsha_employer.user_manager')->findUserByUsernameOrEmail($username);

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

            $userId = $user->getOrganization() ? $user->getOrganization()->getId() : null;

            $contactPerson = $em->getRepository(OrganizationContactPerson::class)->findOneBy(
                [
                    'organization' => $userId,
                    'contactType' => 'contact'
                ]

            );

            if ($contactPerson) {
                $to = $contactPerson->getEmail();
            } else {
                $to = $this->getParameter('default_mailer_address');
            }


            $fullname = $contactPerson->getFirstName() . ' ' . $contactPerson->getLastName();

            $hostname = 'http://' . $request->getHttpHost();

            $message = \Swift_Message::newInstance()
                ->setSubject('Password Resetting')
                ->setFrom('noreply@kantipurjob.com')
                ->setTo($to)
                ->setBody(
                    $this->renderView(
                        'Emails/password_resetting.html.twig',
                        [
                            'name' => $fullname,
                            'host' => $hostname,
                            'token' => $token,
                            'employer' => true
                        ]
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);


            $user->setPasswordRequestedAt(new \DateTime());
            $this->get('yarsha_employer.user_manager')->updateUser($user);

            /* Dispatch completed event */
            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_COMPLETED, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }
        } else {

            $this->addFlash('error', 'Invalid username/email.');

            return $this->redirectToRoute('yarsha_employer_resetting-request');

        }

        return new RedirectResponse($this->generateUrl('yarsha_employer_resetting-check-email',
            ['username' => $username]));
    }

    /**
     * Tell the user to check his email provider.
     *
     * @param Request $request
     * @Route("/email-check/employer", name="yarsha_employer_resetting-check-email")
     * @return Response
     */
    public function checkEmployerEmailAction(Request $request)
    {
        $username = $request->query->get('username');

        if (empty($username)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->generateUrl('yarsha_employer_resetting-request'));
        }

        return $this->render('@YarshaEmployer/UserBundle/Resetting/email-check.html.twig');

    }


    /**
     * Reset user password.
     *
     * @param Request $request
     * @param string $token
     * @Route("/{token}/password-reset/employer", name="yarsha_employer_password-resetting")
     * @return Response
     */
    public function resetAction(Request $request, $token)
    {

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('yarsha_employer.user_manager');
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
                $url = $this->generateUrl('yarsha_employer_homepage');
                $response = new RedirectResponse($url);
            }


            $dispatcher->dispatch(
                FOSUserEvents::RESETTING_RESET_COMPLETED,
                new FilterUserResponseEvent($user, $request, $response)
            );

            return $this->redirectToRoute('yarsha_employer_homepage');
        }

        return $this->render('@YarshaEmployer/UserBundle/Resetting/reset.html.twig', [
            'token' => $token,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @Route("registration/employer/confirmation/{token}", name="yarsha_registration_confirmation_employer")
     */
    public function confirmRegistrationAction(Request $request)
    {
        $token = $request->get('token');

        if (!$token) {
            throw new NotFoundHttpException;
        }

        $em = $this->get('doctrine.orm.entity_manager');

        $user = $em->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);

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

                return $this->redirectToRoute('yarsha_employer_security_login', ['t' => 'p']);

            } catch (\Exception $e) {

            }
        }

        return $this->render('@YarshaEmployer/Registration/activation_failure.html.twig', []);
    }

}
