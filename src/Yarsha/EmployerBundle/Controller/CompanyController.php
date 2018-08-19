<?php

namespace Yarsha\EmployerBundle\Controller;


use Intervention\Image\Exception\NotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Yarsha\EmployerBundle\Event\ProfileUpdateEvent;
use Yarsha\EmployerBundle\Form\EmployerRegisterOrganizationType;
use Yarsha\EmployerBundle\Form\UserprofileType;
use Yarsha\MainBundle\MainBundleEvents;
use Yarsha\OrganizationBundle\Entity\OrganizationContactPerson;
use Yarsha\OrganizationBundle\Form\OrganizationContactPersonType;
use Yarsha\EmployerBundle\Form\PasswordResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;


/**
 * Class CompanyController
 * @package Yarsha\EmployerBundle\Controller
 */
class CompanyController extends Controller
{

    /**
     * @Route("/employer/user/profile", name="yarsha_employer_user_profile" )
     */
    public function userProfileAction(Request $request)
    {

        $employer = $this->getUser();
        $profile = $this->getUser()->getOrganization();

        $form = $this->createForm(UserprofileType::class, $employer);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $userData = $form->getData();

            try {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($userData);
                $em->flush();
                $this->addFlash('success', 'User Profile Update success');

                return $this->redirectToRoute('yarsha_employer_user_profile');

            } catch (\Exception $e) {
                die($e->getMessage());
            }

        }

        $data['form'] = $form->createView();
        $data['profile'] = $profile;

        return $this->render('YarshaEmployerBundle:Employer:user-profile.html.twig', $data);


    }


    /**
     * @Route("/employer/profile", name="yarsha_employer_profile_view" )
     * @Breadcrumb("Profile")
     */
    public function employerProfileAction(Request $request)
    {

        $employer = $this->getUser()->getOrganization();

        $employerHead = $this->getDoctrine()->getRepository(OrganizationContactPerson::class)
            ->findOneBy([
                'organization' => $employer->getId(),
                'contactType' => OrganizationContactPerson::CONTACT_TYPE_HEAD
            ]);

        $employerContactPerson = $this->getDoctrine()->getRepository(OrganizationContactPerson::class)
            ->findOneBy([
                'organization' => $employer->getId(),
                'contactType' => OrganizationContactPerson::CONTACT_TYPE_MAIN_CONTACT
            ]);


        $data['employer'] = $employer;
        $data['head'] = $employerHead;

        $data['contactPerson'] = $employerContactPerson;


        return $this->render('YarshaEmployerBundle:Employer:profile.html.twig', $data);


    }

    /**
     * @Route("employer/edit/info", name="yarsha_employer_profile_change")
     */
    public function changeEmployerInfo(Request $request)
    {
        $employer = $this->getUser()->getOrganization();


        $form = $this->createForm(EmployerRegisterOrganizationType::class, $employer);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $infoData = $form->getData();
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($infoData);
            try {
                $em->flush();
                $event = new ProfileUpdateEvent($this->getUser());
                $eventDispatcher = $this->get('event_dispatcher');
                $eventDispatcher->dispatch(MainBundleEvents::EVENT_EMPLOYER_PROFILE_UPDATE, $event);

                return $this->redirectToRoute('yarsha_employer_profile_view');
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
        $data['form'] = $form->createView();


        return $this->render('YarshaEmployerBundle:Employer:infoUpdate.html.twig', $data);
    }

    /**
     * @Route("employer/edit/{id}/contact-person", name="yarsha_employer_profile_change_contact_person")
     */
    public function changeContactPerson(Request $request)
    {
        $id = $request->get('id');

        $contactPerson = $this->getDoctrine()->getRepository(OrganizationContactPerson::class)->find($id);

        $type = $contactPerson->getContactType();
        $form = $this->createForm(OrganizationContactPersonType::class, $contactPerson,
            ['contact_type' => $type]);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $infoData = $form->getData();
            $infoData->setContactType($infoData->getContactType());
            $this->getDoctrine()->getEntityManager()->persist($infoData);
            try {
                $this->getDoctrine()->getEntityManager()->flush();
                $event = new ProfileUpdateEvent($this->getUser());
                $eventDispatcher = $this->get('event_dispatcher');
                $eventDispatcher->dispatch(MainBundleEvents::EVENT_EMPLOYER_PROFILE_UPDATE, $event);

                return $this->redirectToRoute('yarsha_employer_profile_view');
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
        $data['form'] = $form->createView();

        return $this->render('YarshaEmployerBundle:Employer:contactPersonUpdate.html.twig', $data);

    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/employer/{id}/reset-password", name="yarsha_employer_reset_password")
     * @Breadcrumb("Change Password")
     */

    public function resetPasswordAction(Request $request)
    {

        $id = $request->get('id');

        $user = null;

        $userManager = $this->get('yarsha_employer.user_manager');
        if ($id != '' and is_numeric($id)) {
            $user = $userManager->findUserBy(['id' => $id]);
        }

        $form = $this->createForm(PasswordResetType::class, $user);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $userData = $form->getData();
                $password = $userData->getPlainPassword();
                $user->setPlainPassword($password);
                $userManager->updateUser($user);
                $this->addFlash('success', 'Password reset successfully.');

                return $this->redirectToRoute('yarsha_employer_homepage');
            } else {
                $this->addFlash('error', 'Password do not match with confirm password');

                return $this->redirectToRoute('yarsha_employer_reset_password', ['id' => $id]);
            }
        }

        $data['form'] = $form->createView();

        return $this->render('@YarshaEmployer/Employer/reset_password.html.twig', $data);

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/employer/{id}/-delete-account", name="yarsha_employer_account_deactivate")
     */
    public function deleteAccountAction(Request $request)
    {

        $id = $request->get('id');

        $user = null;
        $userManager = $this->get('yarsha_employer.user_manager');
        if ($id != '' and is_numeric($id)) {
            $user = $userManager->findUserBy(['id' => $id]);

//            $userManager->deleteUser($user);
            $user->setEnabled(false);
            $userManager->updateUser($user);
        }

        return $this->redirectToRoute('yarsha_employer_account_deactivate_success');


    }

    /**
     * @Route("/deactivate-success", name="yarsha_employer_account_deactivate_success" )
     */
    public function deactivateSuccessAction(Request $request)
    {

        return $this->render('@YarshaEmployer/Home/deactivate.html.twig');

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/employer/followers", name="yarsha_employer_followers")
     * @Breadcrumb("Followers")
     */

    public function FollowersAction(Request $request)
    {
        $organization = $this->getUser()->getOrganization();
        $total_follower = $organization->getFollowers();
        $data['followers'] = $total_follower;

        return $this->render('@YarshaEmployer/Employer/followers.html.twig', $data);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/employer/appliers", name="yarsha_employer_appliers")
     * @Breadcrumb("Applicants")
     */

    public function AppliersAction(Request $request)
    {
        $employerId = $this->getUser()->getId();
        $employerService = $this->get('yarsha.service.employer');
        $total_applicant = $employerService->getPaginatedTotalApplicant($employerId);

        $data['applicants'] = $total_applicant;

        return $this->render('@YarshaEmployer/Employer/applicants.html.twig', $data);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/employer/follower/{username}", name="yarsha_employer_follower_details")
     * @Breadcrumb("Follower",routeName="yarsha_employer_followers")
     */

    public function FollowerDetailsAction(Request $request)
    {
        $username = $request->get('username');
        $seekerService = $this->get('yarsha.service.job_seeker');
        $seeker = $seekerService->getSeekerByUsername($username);

        if (!$seeker) {
            throw new NotFoundException;
        }

        $education = $seekerService->getSeekerEducation($seeker);
        $data['educations'] = $education;

        $data['detail'] = $data['seeker'] = $seeker;
        $this->get('apy_breadcrumb_trail')->add($seeker->getFirstName());

        return $this->render('@YarshaEmployer/Jobs/search-applicant-detail.html.twig', $data);
    }


}
