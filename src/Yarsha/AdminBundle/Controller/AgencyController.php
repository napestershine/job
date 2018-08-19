<?php

namespace Yarsha\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Yarsha\JobSeekerBundle\Entity\EmployeeAppliedJob;
use Yarsha\JobSeekerBundle\Form\JobSeekerRegistrationType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yarsha\AgencyBundle\Form\UserType;

/**
 * Class EmployeeController
 * @package Yarsha\AdminBundle\Controller
 * @Breadcrumb("Agency", routeName="yarsha_admin_agency_list")
 */
class AgencyController extends Controller
{

    /**
     * @Route("admin/agency/list", name="yarsha_admin_agency_list")
     * @Breadcrumb("list")
     */
    public function agencylistAction(Request $request)
    {

        $filters = $request->query->all();
        $agencyService = $this->get('yarsha.service.admin_agency');
        $agencies = $agencyService->getPaginatedAgencyList($filters);
        $data['agencies'] = $agencies;

        return $this->render('YarshaAdminBundle:Agency:list.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/agency/add", name="yarsha_admin_agency_add")
     * @Route("/admin/agency/{id}/edit", name="yarsha_admin_agency_edit")
     */
    public function addAgencyAction(Request $request)
    {
        $mailerService = $this->get('yarsha.service.mailer');

        $data['is_updating'] = false;
        $userManager = $this->get('yarsha_agency.user_manager');
        $id = $request->get('id');
        if ($id) {
            $data['is_updating'] = true;
            $agency = $this->get('yarsha.service.admin_agency')->getAgencyById($id);
            $options['is_updating'] = true;
        } else {
            $agency = $userManager->createUser();
            $options['is_updating'] = false;
        }
        $form = $this->createForm(UserType::class, $agency,$options);


        $form->handleRequest($request);

        $breadcrumb = $this->get('apy_breadcrumb_trail');
        $breadcrumb->add($data['is_updating'] ? $agency->getName() : 'New');

        if ($form->isSubmitted() and $form->isValid()) {
            try {

                $token = bin2hex(openssl_random_pseudo_bytes(16));

                $user = $form->getData();
                if ($data['is_updating'] == false) {
                    $user->setEnabled(true);
                    $user->setPlainPassword($user->getPassword());
                    $user->addRole('ROLE_AGENCY');
                    $username = $user->getUsername();
                    $user->setUsername($username);
                    $user->setAccessToken($token);
                }

                $userManager->updateUser($user);
                $this->get('doctrine.orm.entity_manager')->flush();

                $subject = 'Agency Access Token';
                $body = '<p>Account created successfully.</p><p>Access Token:'.$token.'</p>';
                $to =$user->getEmail();

                if ($data['is_updating'] == false) {
                    $mailerService->sendEmail($subject, $body, $to);
                }


                $message = $data['is_updating'] ? 'Agency successfully updated.' : 'Agency successfully added.';
                $this->addFlash('success', $message);

                return $this->redirectToRoute('yarsha_admin_agency_list');

            } catch
            (\Exception $e) {
                $data['errorMessage'] = $e->getMessage();
            }
        }
        $data['form'] = $form->createView();

        return $this->render('YarshaAdminBundle:Agency:add.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/admin/ajax/agency/delete", name="yarsha_admin_ajax_agency_delete")
     */
    public function deleteAgencyAction(Request $request)
    {
        $agencyId = $request->get('agency');
        $agency = $this->get('yarsha.service.admin_agency')->getAgencyById($agencyId);
        if (!$agency) {
            return new JsonResponse(['status' => 'error', 'message' => 'No Agency Found'], 400);
        }

        try {
            $em = $this->get('doctrine.orm.entity_manager');
            $agency->setDeleted(true);
            $agency->setEnabled(false);
            $em->persist($agency);
            $em->flush();
            $this->addFlash('success', 'Agency Deleted Successfully.');
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        }

        return new JsonResponse(['status' => 'success']);
    }

    /**
     * @Route("admin/agencyJob/list", name="yarsha_admin_agency_job_list")
     * @Breadcrumb("Job List")
     */
    public function agencyJoblistAction(Request $request)
    {

        $filters = $request->query->all();
        $agencyService = $this->get('yarsha.service.admin_agency');
        $agencies = $agencyService->getPaginatedAgencyJobList($filters);
        $data['jobs'] = $agencies;

        return $this->render('YarshaAdminBundle:Agency:joblist.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/admin/ajax/agencyJob/delete", name="yarsha_admin_ajax_agency_job_delete")
     */
    public function deleteAgencyJobAction(Request $request)
    {
        $Id = $request->get('id');
        $job = $this->get('yarsha.service.admin_agency')->getAgencyJobById($Id);
        if (!$job) {
            return new JsonResponse(['status' => 'error', 'message' => 'No Job Found'], 400);
        }

        try {
            $em = $this->get('doctrine.orm.entity_manager');
            $job->setDeleted(true);
            $em->persist($job);
            $em->flush();
            $this->addFlash('success', 'Agency Job Deleted Successfully.');
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        }

        return new JsonResponse(['status' => 'success']);
    }



}
