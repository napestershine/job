<?php

namespace Yarsha\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yarsha\AdminBundle\Form\GovermentJobType;
use Yarsha\AdminBundle\Form\GovermentOrganizationFormType;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\JobsBundle\JobConstants;
use Yarsha\OrganizationBundle\Entity\Organization;
use Symfony\Component\HttpFoundation\JsonResponse;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Class GovermentJobsController
 * @package Yarsha\AdminBundle\Controller
 *
 * @Route("/admin")
 * @Breadcrumb("Government Job",routeName="yarsha_admin_goverment_jobs_list")
 */
class GovermentJobsController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/goverment-jobs", name="yarsha_admin_goverment_jobs_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->query->all();

        $data['jobs'] = $this
            ->get('yarsha.service.goverment_job')
            ->getGovermentJobsPaginatedList($filters);

        return $this->render("YarshaAdminBundle:GovermentJobs:list.html.twig", $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/goverment-job/new", name="yarsha_admin_goverment_job_new")
     * @Route("/goverment-job/{id}/update", name="yarsha_admin_goverment_job_update")
     */
    public function createAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data['isUpdating'] = false;

        $user = $this->getUser();

        $id = $request->get('id');

        if ($id) {
            $job = $this->get('yarsha.service.job')->getJobById($id);

            if (!$job or $job->getJobsFrom() != JobConstants::JOB_FROM_GOVERNMENT) {
                throw new NotFoundHttpException;
            }

            $data['isUpdating'] = true;

        } else {
            $job = new Job();
        }

        $form = $this->createForm(GovermentJobType::class, $job);

        $form->handleRequest($request);
        $this->get('apy_breadcrumb_trail')->add($data['isUpdating'] ? $job->getTitle() : 'New');


        if ($form->isSubmitted() and $form->isValid()) {
            $job = $form->getData();

            try {

                if ($data['isUpdating'] == false) {

                    $job->setJobsFrom(JobConstants::JOB_FROM_GOVERNMENT);
                    $job->setUserId($user->getId());
                    $job->setUsername($user->getUsername());
                }

                $job->upload();
                $em = $this->get('doctrine.orm.entity_manager');
                $em->persist($job);
                $em->flush();
                $this->addFlash('success', 'Goverment job Saved Successfully,');

                return $this->redirectToRoute('yarsha_admin_goverment_jobs_list');
            } catch (\Exception $e) {
                $data['errorMessage'] = $e->getMessage();
            }
        }

        $data['form'] = $form->createView();

        return $this->render("YarshaAdminBundle:GovermentJobs:create.html.twig", $data);
    }

    /**
     * @Route("/goverment-job/{id}/detail",name="yarsha_admin_goverment_job_detail")
     */
    public function viewAction(Request $request)
    {
        $jobId = $request->get('id');
        $jobService = $this->get('yarsha.service.job');
        $job = $jobService->getJobById($jobId);
        $data['job'] = $job;
        $this->get('apy_breadcrumb_trail')->add($job->getTitle());

        return $this->render('YarshaAdminBundle:GovermentJobs:view.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/goverment-job/{id}/delete", name="yarsha_admin_goverment_job_delete")
     */
    public function deleteAction(Request $request)
    {

        $jobId = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $jobRepo = $em->getRepository('YarshaJobsBundle:Job');
        $job = $jobRepo->find($jobId);
        $response = [];
        if ($job) {
            $job->deActivate();
            $this->getDoctrine()->getEntityManager()->persist($job);
            try {
                $this->getDoctrine()->getEntityManager()->flush();
                $response['status'] = 'success';
            } catch (\Exception $e) {
                $response['status'] = $e->getMessage();
            }
        }

        return new JsonResponse($response);
    }

    /**
     *
     * @Route("/goverment-job/{id}/change-status",name="yarsha_admin_approve_goverment_job")
     */
    public function approveAction(Request $request)
    {
        $jobId = $request->get('id');
        $jobService = $this->get('yarsha.service.job');
        $job = $jobService->getJobById($jobId);

        if ($job) {
            $org = $job->getUserId();
            $employer = $jobService->getEmployerById($org);
            $email_to = $employer->getEmail();
            $to = 'awaley03@gmail.com';
            $name = $job->getOrganization();
            $jobService->changeStatus($job->getStatus(), $jobId);

            $message = \Swift_Message::newInstance()
                ->setSubject('Job Post')
                ->setFrom('amika.awale@yarshastudio.com')
                ->setTo($to)
                ->setBody(
                    $this->renderView(
                        'Emails/approve.html.twig',
                        ['name' => $name]
                    ),
                    'text/html'
                );
            //$this->get('mailer')->send($message);

            $this->addFlash('success', 'Updated successfully');

            return $this->redirectToRoute('yarsha_admin_goverment_jobs_list');

        } else {
            $this->addFlash('error', 'Something wrong');

            return $this->redirectToRoute('yarsha_admin_goverment_jobs_list');
        }


    }

}


