<?php

namespace Yarsha\AdminBundle\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Yarsha\EmployerBundle\Entity\User;
use Yarsha\EmployerBundle\Form\EmployerLoginCredentialType;
use Yarsha\EmployerBundle\Form\EmployerRegisterOrganizationType;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\JobsBundle\Entity\JobSetting;
use Yarsha\JobSeekerBundle\Entity\EmployeeAppliedJob;
use Yarsha\OrganizationBundle\Entity\Organization;
use Yarsha\OrganizationBundle\Entity\OrganizationAutoEmailResponder;
use Yarsha\OrganizationBundle\Entity\OrganizationSetting;
use Yarsha\OrganizationBundle\Entity\OrganizationSize;
use Yarsha\OrganizationBundle\Entity\OrganizationType;
use Yarsha\OrganizationBundle\Form\OrganizationContactPersonType;
use Yarsha\OrganizationBundle\Form\OrganizationSizeType;
use Yarsha\OrganizationBundle\Form\OrganizationTypeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Yarsha\OrganizationBundle\Entity\OrganizationOwnership;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Yarsha\OrganizationBundle\Form\OrganizationOwnershipType;
use Yarsha\OrganizationBundle\Entity\OrganizationContactPerson;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Yarsha\OrganizationBundle\OrganizationConstants;
use Yarsha\OrganizationBundle\Entity\OrganizationBannerImages;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use FOS\UserBundle\FOSUserEvents;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;


/**
 * Class OrganizationController
 * @package Yarsha\AdminBundle\Controller
 *
 * @Breadcrumb("Employers", routeName="yarsha_admin_organization_list")
 */
class OrganizationController extends Controller
{

    private $employer;

    private $organization;

    private $contactPerson;

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/admin/organization/list", name="yarsha_admin_organization_list")
     * @Breadcrumb("list")
     */
    public function indexAction(Request $request)
    {
        $form = $this->getEmailSendToEmployerForm();
        $filters = $request->query->all();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $to = $formData['email'];
            $textmessage = $formData['message'];

            $message = \Swift_Message::newInstance()
                ->setSubject('Message from Kantipur Job')
                ->setFrom('noreply@kantipurjob.com', 'Kantipur Job')
                ->setTo($to)
                ->setBody(
                    $this->renderView(
                        'Emails/email_to_organization.html.twig',
                        [
                            'message' => $textmessage
                        ]
                    ),
                    'text/html'
                );

            try {
                $this->get('mailer')->send($message);
                $this->addFlash('success', 'Email successfully sent to employer.');
            } catch (\Exception $e) {
                $data['errorMessage'] = $e->getMessage();
            }

            return $this->redirectToRoute('yarsha_admin_organization_list');
        }

        $organizations = $this->get('yarsha.service.organization')->getPaginatedOrganizationList($filters);
        $data['organizations'] = $organizations;
        $data['form'] = $form->createView();

        return $this->render('YarshaAdminBundle:Organization:list.html.twig', $data);

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/admin/organization/{id}/detail", name="yarsha_admin_organization_detail")
     */
    public function detailAction(Request $request)
    {
        $id = $request->get('id');
        $organizationService = $this->get('yarsha.service.organization');
        $data['organization'] = $organization = $organizationService->getOrganizationById($id);

        if (!$organization) {
            throw new NotFoundHttpException;
        }

        $this->get('apy_breadcrumb_trail')->add($organization->getName());

        $data['contactPersonTypeContact'] = null;
        $data['contactPersonTypeHead'] = null;

        $contactPersons = $organizationService->getContactPersonByOrganizationId($id);
        foreach ($contactPersons as $contactPerson) {
            if ($contactPerson->getContactType() == OrganizationContactPerson::CONTACT_TYPE_MAIN_CONTACT) {
                $data['contactPersonTypeContact'] = $contactPerson;
            }

            if ($contactPerson->getContactType() == OrganizationContactPerson::CONTACT_TYPE_HEAD) {
                $data['contactPersonTypeHead'] = $contactPerson;
            }
        }


        return $this->render('YarshaAdminBundle:Organization:detail.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/organization/add", name="yarsha_admin_organization_add")
     * @Route("/admin/organization/{id}/edit", name="yarsha_admin_organization_edit")
     */
    public function addOrganizationAction(Request $request)
    {
        $data['isUpdating'] = false;
        $id = $request->get('id');
        if ($id) {
            $data['isUpdating'] = true;
            $this->organization = $this->get('yarsha.service.organization')->getOrganizationById($id);
            $contactPerson = $this->get('yarsha.service.organization')->getContactPersonByOrganizationId($this->organization->getId());
            $this->contactPerson = $contactPerson[0];
        } else {
            $this->organization = new Organization();
            $this->employer = new User();
            $this->contactPerson = new OrganizationContactPerson();
        }
        $form = $this->buildEmployerRegisterForm($data['isUpdating']);
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
                $organization->setStatus(OrganizationConstants::ORGANIZATION_STATUS_APPROVED);
                $organization->setEmail($organizationContactPerson->getEmail());
                $em = $this->get('doctrine.orm.entity_manager');

                $em->persist($organization);

                $organizationContactPerson->setOrganization($organization);
                $em->persist($organizationContactPerson);

                if ($data['isUpdating'] == false) {
                    $userDiscriminator = $this->get('rollerworks_multi_user.user_discriminator');
                    $userDiscriminator->setCurrentUser('yarsha_employer');

                    $userManager = $this->get('yarsha_employer.user_manager');
                    $employer = $formData['user_login_credential'];

                    $username = $employer->getUsername();
                    $password = $employer->getPassword();
                    $email = (strpos($username, '@')) ? $username : $username . '@kantipurjob.com';

                    $employer->setUsername($username);
                    $employer->setEmail($email);
                    $employer->setPlainPassword($password);
                    $employer->setEnabled(true);
                    $employer->addRole('ROLE_EMPLOYER');
                    $employer->setOrganization($organization);

                    $userManager->updateUser($employer);
                }

                $em->flush();


                $message = $data['isUpdating'] ? 'Organization successfully updated.' : 'Organization successfully added.';
                $this->addFlash('success', $message);

                return $this->redirectToRoute('yarsha_admin_organization_list');

            } catch (\Exception $e) {
                $data['errorMessage'] = $e->getMessage();
            }
        }
        $data['form'] = $form->createView();

        return $this->render('YarshaAdminBundle:Organization:addorganization.html.twig', $data);
    }

    private function buildEmployerRegisterForm($isUpdating)
    {
        $builder = $this->createFormBuilder();

        $builder->add('organization', EmployerRegisterOrganizationType::class, [
            'label_attr' => [
                'class' => 'hidden'
            ],
            'admin' => true,
            'data' => $this->organization
        ])
            ->add('organization_contact_person', OrganizationContactPersonType::class, [
                'contact_type' => OrganizationContactPerson::CONTACT_TYPE_MAIN_CONTACT,
                'label_attr' => [
                    'class' => 'hidden'
                ],
                'admin' => true,
                'data' => $this->contactPerson
            ]);

        if (!$isUpdating) {

            $builder
                ->add('user_login_credential', EmployerLoginCredentialType::class, [
                    'label_attr' => [
                        'class' => 'hidden'
                    ],
                    'admin' => true
                ]);
        }

        return $builder->getForm();
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/admin/organization/{id}/jobs", name="yarsha_admin_organization_job_list")
     */
    public function jobListAction(Request $request)
    {
        $id = $request->get('id');

        $filters = $request->query->all();

        $filters['organization'] = $id;

        if (!isset($filters['status']) or $filters['status'] == '') {
            $filters['showAll'] = true;
        }

        $organizationService = $this->get('yarsha.service.organization');
        $data['organization'] = $organization = $organizationService->getOrganizationById($id);
        if (!$organization) {
            throw new NotFoundHttpException;
        }
        $this->get('apy_breadcrumb_trail')->add($organization->getName(), 'yarsha_admin_organization_detail',
            ['id' => $id]);
        $this->get('apy_breadcrumb_trail')->add("Posted Jobs");
        $data['jobs'] = $this->get('yarsha.service.job')->getPaginatedJobList($filters);

        return $this->render('YarshaAdminBundle:Job:list.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/organizationownership/create", name="yarsha_admin_organization_ownership_add")
     * @Route("/admin/organizationownership/{id}/edit", name="yarsha_admin_organization_ownership_edit")
     */
    public function addOrganizationOwnershipAction(Request $request)
    {
        $data['updating'] = false;
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        if ($id) {
            $data['updating'] = true;
            $organizationOwnership = $em->getRepository(OrganizationOwnership::class)->find($id);
            if (!$organizationOwnership) {
                throw new NotFoundHttpException();
            }
        } else {
            $organizationOwnership = new OrganizationOwnership();
        }
        $form = $this->createForm(OrganizationOwnershipType::class, $organizationOwnership);

        $form->handleRequest($request);
        $this->get('apy_breadcrumb_trail')->add('Organization Ownership', 'yarsha_admin_organization_ownership_list');
        $this->get('apy_breadcrumb_trail')->add($data['updating'] ? $organizationOwnership->getName() : 'New');

        if ($form->isSubmitted() && $form->isValid()) {
            $organizationOwnership = $form->getData();
            $organizationOwnership->setCreatedBy($this->getUser());
            $em->persist($organizationOwnership);
            try {
                $em->flush();
                $status = $data['updating'] ? "updated" : "added";
                $this->addFlash('success', "Organization ownership {$status}.");

                return $this->redirectToRoute('yarsha_admin_organization_ownership_list');
            } catch (\Exception $e) {
                $this->addFlash('errorMessage', $e->getMessage());
            }
        }

        $data['form'] = $form->createView();

        return $this->render('@YarshaAdmin/Organization/addorganizationownership.html.twig', $data);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/organizationownership/list", name="yarsha_admin_organization_ownership_list")
     */
    public function listOrganizationOwnershipAction()
    {
        $data['organizationOwnerships'] = $this->getDoctrine()->getManager()->getRepository(OrganizationOwnership::class)->findAll();
        $this->get('apy_breadcrumb_trail')->add('Organization Ownership', 'yarsha_admin_organization_ownership_list');
        $this->get('apy_breadcrumb_trail')->add("List");

        return $this->render('YarshaAdminBundle:Organization:organizationownershiplist.html.twig', $data);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/organizationownership/{id}/delete", name="yarsha_admin_organization_ownership_delete");
     */
    public function deleteOrganizationOwnershipAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $organizationOwnership = $em->getRepository(OrganizationOwnership::class)->find($id);
        if (!$organizationOwnership) {
            throw new NotFoundHttpException();
        }
        $em->remove($organizationOwnership);
        try {
            $em->flush();
            $this->addFlash('success', 'One Organization ownership deleted.');
        } catch (\Exception $e) {
            $this->addFlash('errorMessage', $e->getMessage());
        }

        return $this->redirectToRoute('yarsha_admin_organization_ownership_list');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/organizationsize/create", name="yarsha_admin_organization_size_add")
     * @Route("/admin/organizationsize/{id}/edit", name="yarsha_admin_organization_size_edit")
     */
    public function addOrganizationSizeAction(Request $request)
    {
        $data['updating'] = false;
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        if ($id) {
            $data['updating'] = true;
            $organizationSize = $em->getRepository(OrganizationSize::class)->find($id);
            if (!$organizationSize) {
                throw new NotFoundHttpException();
            }
        } else {
            $organizationSize = new OrganizationSize();
        }
        $form = $this->createForm(OrganizationSizeType::class, $organizationSize);

        $form->handleRequest($request);
        $this->get('apy_breadcrumb_trail')->add('Organization Size', 'yarsha_admin_organization_size_list');
        $this->get('apy_breadcrumb_trail')->add($data['updating'] ? $organizationSize->getName() : 'New');

        if ($form->isSubmitted() && $form->isValid()) {
            $organizationSize = $form->getData();
            $organizationSize->setCreatedBy($this->getUser());
            $em->persist($organizationSize);
            try {
                $em->flush();
                $status = $data['updating'] ? "updated" : "added";
                $this->addFlash('success', "Organization size {$status}.");

                return $this->redirectToRoute('yarsha_admin_organization_size_list');
            } catch (\Exception $e) {
                $this->addFlash('errorMessage', $e->getMessage());
            }
        }

        $data['form'] = $form->createView();

        return $this->render('@YarshaAdmin/Organization/addorganizationsize.html.twig', $data);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/organizationsize/list", name="yarsha_admin_organization_size_list")
     */
    public function listOrganizationSizeAction()
    {
        $organizationSizes = $this->getDoctrine()->getManager()->getRepository(OrganizationSize::class)->findAll();
        $data['organizationSizes'] = $organizationSizes;
        $this->get('apy_breadcrumb_trail')->add('Organization Size', 'yarsha_admin_organization_size_list');
        $this->get('apy_breadcrumb_trail')->add("List");

        return $this->render('YarshaAdminBundle:Organization:organizationsizelist.html.twig', $data);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/organizationsize/{id}/delete", name="yarsha_admin_organization_size_delete");
     */
    public function deleteOrganizationSizeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $organizationSize = $em->getRepository(OrganizationSize::class)->find($id);
        if (!$organizationSize) {
            throw new NotFoundHttpException();
        }
        $em->remove($organizationSize);
        try {
            $em->flush();
            $this->addFlash('success', 'One Organization Size deleted.');
        } catch (\Exception $e) {
            $this->addFlash('errorMessage', $e->getMessage());
        }

        return $this->redirectToRoute('yarsha_admin_organization_size_list');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/organizationtype/create", name="yarsha_admin_organization_type_add")
     * @Route("/admin/organizationtype/{id}/edit", name="yarsha_admin_organization_type_edit")
     */
    public function addOrganizationTypeAction(Request $request)
    {
        $data['updating'] = false;
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        if ($id) {
            $data['updating'] = true;
            $organizationType = $em->getRepository(OrganizationType::class)->find($id);
            if (!$organizationType) {
                throw new NotFoundHttpException();
            }
        } else {
            $organizationType = new OrganizationType();
        }
        $form = $this->createForm(OrganizationTypeType::class, $organizationType);

        $form->handleRequest($request);
        $this->get('apy_breadcrumb_trail')->add('Organization Type', 'yarsha_admin_organization_type_list');
        $this->get('apy_breadcrumb_trail')->add($data['updating'] ? $organizationType->getName() : 'New');

        if ($form->isSubmitted() && $form->isValid()) {
            $organizationType = $form->getData();
            $organizationType->setCreatedBy($this->getUser());
            $em->persist($organizationType);
            try {
                $em->flush();
                $status = $data['updating'] ? "updated" : "added";
                $this->addFlash('success', "Organization type {$status}.");

                return $this->redirectToRoute('yarsha_admin_organization_type_list');
            } catch (\Exception $e) {
                $this->addFlash('errorMessage', $e->getMessage());
            }
        }

        $data['form'] = $form->createView();

        return $this->render('@YarshaAdmin/Organization/addorganizationtype.html.twig', $data);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/organizationtype/list", name="yarsha_admin_organization_type_list")
     */
    public function listOrganizationTypeAction()
    {
        $organizationTypes = $this->getDoctrine()->getManager()->getRepository(OrganizationType::class)->findAll();
        $data['organizationTypes'] = $organizationTypes;
        $this->get('apy_breadcrumb_trail')->add('Organization Type', 'yarsha_admin_organization_type_list');
        $this->get('apy_breadcrumb_trail')->add("List");

        return $this->render('YarshaAdminBundle:Organization:organizationtypelist.html.twig', $data);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/organizationtype/{id}/delete", name="yarsha_admin_organization_type_delete");
     */
    public function deleteOrganizationTypeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $organizationType = $em->getRepository(OrganizationSize::class)->find($id);
        if (!$organizationType) {
            throw new NotFoundHttpException();
        }
        $em->remove($organizationType);
        try {
            $em->flush();
            $this->addFlash('success', 'One Organization Type deleted.');
        } catch (\Exception $e) {
            $this->addFlash('errorMessage', $e->getMessage());
        }

        return $this->redirectToRoute('yarsha_admin_organization_type_list');
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/admin/ajax/employer/delete", name="yarsha_admin_ajax_employer_delete")
     */
    public function deleteEmployerAction(Request $request)
    {
        $organizationId = $request->get('employer');
        $organization = $this->get('yarsha.service.organization')->getOrganizationById($organizationId);
        if (!$organization) {
            return new JsonResponse(['status' => 'error', 'message' => 'No Employer Found'], 400);
        }

        try {
            $em = $this->get('doctrine.orm.entity_manager');
            $organization->setDeleted(true);
            $employerUser = $this->get('yarsha.service.employer')->getEmployerByOrganization($organization);
            if ($employerUser) {
                $employerUser->setEnabled(false);
                $em->persist($employerUser);
            }
            $em->persist($organization);
            $em->flush();
            $this->addFlash('success', 'Employer Deleted Successfully.');
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        }

        return new JsonResponse(['status' => 'success']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/admin/ajax/employer/account-manager/change", name="yarsha_admin_ajax_change_account_manager_for_employer")
     */
    public function changeAccountManagerForEmployerAction(Request $request)
    {
        if ("POST" !== $request->getMethod()) {
            return new JsonResponse(['message' => 'Bad Request'], 400);
        }
        $organization = $this->get('yarsha.service.organization')->getOrganizationById($request->get('organization'));
        if (!$organization) {
            return new JsonResponse(['message' => 'Bad Request'], 400);
        }
        $previousValue = $request->get('previousManager');
        $selectedValue = $request->get('newManager');
        $response['status'] = 'success';

        try {
            $newManager = $this->get('yarsha.service.admin_user')->getAdminUserById($selectedValue);

            $organization->setAccountManager($newManager);
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($organization);
            $em->flush();
            $response['template'] = $this->get('yarsha.twig.organization_admin')->renderAccountManager($newManager);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        }

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/organization/{id}/banners", name="yarsha_admin_organization_banners")
     */
    public function organizationBannersAction(Request $request)
    {

        $data = [];
        $orgId = $request->get('id');
        $organizationService = $this->get('yarsha.service.organization');
        $data['organization'] = $organization = $organizationService->getOrganizationById($orgId);
        if ($orgId) {

            $em = $this->get('doctrine.orm.entity_manager');
            $banners = $em->getRepository(OrganizationBannerImages::class)->findBy(

                ['employer' => $orgId],
                ['order' => 'ASC']


            );
        }
        $this->get('apy_breadcrumb_trail')->add($organization->getName());
        $this->get('apy_breadcrumb_trail')->add('banners');

        $data['banners'] = $banners;

        return $this->render('YarshaAdminBundle:Banners:index.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("admin/trashed/employers", name="yarsha_admin_trashed_employers_list")
     */
    public function listTrashsedEmployersAction(Request $request)
    {
        $filters = $request->query->all();
        $trashedEmployers = $this->getDoctrine()->getManager()->getRepository(Organization::class)->getTrashedOrganizations($filters);
        $data['trashedEmployers'] = $trashedEmployers;

        return $this->render('@YarshaAdmin/Organization/trashedEmployers.html.twig', $data);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/admin/ajax/delete/{id}/employer/permanently", name="yarsha_admin_ajax_delete_employer_permenently")
     */
    public function deleteEmployerPermanently(Request $request)
    {
        $id = $request->get('id');
        $data['success'] = false;
        $em = $this->get('doctrine.orm.default_entity_manager');
        $organization = $em->getRepository(Organization::class)->find($id);

        if ($organization) {
            try {

                $contactPerson = $em->getRepository(OrganizationContactPerson::class)->findBy(
                    ['organization' => $organization]
                );
                $this->removeElements($contactPerson);

                $settings = $em->getRepository(Job::class)->getJobSettings($organization);
                $this->removeElements($settings);

                $appliedJobs = $em->getRepository(EmployeeAppliedJob::class)->getLatestJobsAppliedbySeekerEmployer($organization);
                $this->removeElements($appliedJobs);


                $postedJobs = $em->getRepository(Job::class)->getJobsByEmployer($organization);
                $this->removeElements($postedJobs);


                $banners = $em->getRepository(OrganizationBannerImages::class)->getBannersToDelete($organization);
                $this->removeElements($banners);


                $emplopyer = $em->getRepository(User::class)->findOneBy(
                    ['organization' => $organization]
                );
                if ($emplopyer) {
                    $em->remove($emplopyer);
                    $em->flush();
                }


                $em->getRepository(Organization::class)->removeFollowedOrganizationBySeeker($organization->getId());


                $detachedEntity = $em->merge($organization);
                $em->remove($detachedEntity);
                $em->flush();

                $data['success'] = true;
                $data['message'] = "Employer deleted permanently.";
            } catch (\Exception $e) {
                $data['errorMessage'] = $e->getMessage();
            }
        }

        return new JsonResponse($data);
    }


    public function removeElements($collections)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        foreach ($collections as $collection) {
            $em->remove($collection);
        }
        $em->flush();
        $em->clear();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/admin/ajax/restore/{id}/employer", name="yarsha_admin_ajax_restore_employer")
     */
    public function restoreTrashedEmployerAction(Request $request)
    {
        $data['success'] = false;
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $employerService = $this->get('yarsha.service.employer');
        $organization = $em->getRepository(Organization::class)->find($id);
        $employer = $employerService->getEmployerByOrganization($organization);
        if ($employer and $organization) {
            $organization->setDeleted(false);
            $em->persist($employer);
            try {
                $em->flush();
                $data['message'] = "Employer successfully restored.";
                $data['success'] = true;
            } catch (\Exception $e) {
                $data['errorMessage'] = "Something went wrong. Unable to restore employer.";
            }
        } else {
            $data['errorMessage'] = "Employer does not exist.";
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/admin/organization/{id}/changepassword", name="yarsha_admin_change_employer_password")
     */
    function changeEmployerPassword(Request $request)
    {
        $id = $request->get('id');
        $em = $this->get('doctrine.orm.entity_manager');
        $user = $em->getRepository(User::class)->findOneBy(['organization' => $id]);
        if (!$user) {
            throw new NotFoundHttpException();
        }
        $form = $this->getPasswordResetForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userData = $form->getData();
            $user->setPlainPassword($userData['plainPassword']);
            $userManager = $this->get('yarsha_employer.user_manager');
            try {
                $userManager->updateUser($user);
                $eventDispatcher = $this->get('event_dispatcher');
                $eventDispatcher->dispatch(FOSUserEvents::USER_PASSWORD_CHANGED);
                $this->addFlash('success', 'Password Changed. User new password in next login.');
            } catch (\Exception $e) {
                $data['errorMessage'] = $e->getMessage();
            }

            return $this->redirectToRoute('yarsha_admin_organization_list');
        }
        $this->get('apy_breadcrumb_trail')->add($user->getOrganization()->getName());
        $data['title'] = 'Employer';
        $data['form'] = $form->createView();

        return $this->render('@YarshaAdmin/employee/resetpassword.html.twig', $data);
    }

    public function getPasswordResetForm()
    {
        $form = $this->createFormBuilder()
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password and confirm password field must match.',
                'options' => [
                    'attr' => [
                        'class' => 'password-field',
                        'pattern' => '(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}',
                        'title' => "Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"
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

        return $form;
    }

    public function getEmailSendToEmployerForm()
    {
        $form = $this->createFormBuilder()
            ->add('email', null, [
                'attr' => [
                    'label' => 'Email To',
                ]
            ])
            ->add('message', CKEditorType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->getForm();

        return $form;
    }


}
