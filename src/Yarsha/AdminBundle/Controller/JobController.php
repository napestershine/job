<?php

namespace Yarsha\AdminBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Proxies\__CG__\Yarsha\JobSeekerBundle\Entity\EmployeeAppliedJob;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yarsha\EmployerBundle\YarshaEmployerEvents;
use Yarsha\JobsBundle\Entity\Job;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Yarsha\JobsBundle\Entity\JobSetting;
use Yarsha\JobsBundle\Form\JobType;
use Yarsha\JobsBundle\JobConstants;
use Yarsha\MainBundle\Event\EmployerJobsEmailEvent;
use Yarsha\MainBundle\MainBundleEvents;
use Yarsha\OrganizationBundle\OrganizationConstants;


/**
 * Class JobController
 * @package Yarsha\AdminBundle\Controller
 *
 * @Route("/admin")
 *
 */
class JobController extends Controller
{

    /**
     * @Route("/job/list", name="yarsha_admin_job_list")
     * @Breadcrumb("Jobs", routeName="yarsha_admin_job_list")
     * @Breadcrumb("list")
     */
    public function indexAction(Request $request)
    {
        $filters = $request->query->all();
        $jobs = $this->get('yarsha.service.job')->getPaginatedJobList($filters);
        $data['jobs'] = $jobs;

        return $this->render('YarshaAdminBundle:Job:list.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/job/add", name="yarsha_admin_job_add")
     * @Route("/job/{id}/edit", name="yarsha_admin_job_edit")
     */
    public function addJobAction(Request $request)
    {

        $id = $request->get('id');

        $data['isUpdating'] = false;

        if ($id) {

            $job = $this->get('yarsha.service.job')->getJobById($id);

            if (!$job) {
                throw new NotFoundHttpException;
            }

            $data['isUpdating'] = true;

        } else {
            $job = new Job();
        }

        $form = $this->createForm(JobType::class, $job, ['show_organization' => true]);

        $breadcrumb = $this->get('apy_breadcrumb_trail');

        if ($organizationId = $request->get('ref')) {
            $organization = $this->get('yarsha.service.organization')
                ->getOrganizationById($organizationId);
            if ($organization) {
                $form->get('organization')->setData($organization);
            }
            $breadcrumb->add($organization->getName(), 'yarsha_admin_organization_detail', ['id' => $organizationId]);
            $breadcrumb->add("Posted Jobs", 'yarsha_admin_organization_job_list', ['id' => $organizationId]);
        } else {
            $breadcrumb->add("Posted Jobs", 'yarsha_admin_job_list');
        }

        $breadcrumb->add($data['isUpdating'] ? $job->getTitle() : 'New');

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {

            try {
                $job = $form->getData();

                if ($data['isUpdating'] == false) {
                    $job->setJobsFrom(JobConstants::JOB_FROM_EMPLOYERS);
                    $job->setUserId($this->getUser()->getId());
                    $job->setUsername($this->getUser()->getUsername());
                }


                $jobFile = $job->getFile();
                if ($jobFile) {
                    $job->upload();
                }


                $em = $this->get('doctrine.orm.entity_manager');
                $em->persist($job);

                $jobSettings = $job->getSettings();
                $jobSettings->setJob($job);

                $em->flush();

                $successMessage = $data['isUpdating'] ? 'Job Updated Successfully.' : 'Job Added Successfully.';

                $this->addFlash('success', $successMessage);

                return $this->redirectToRoute('yarsha_admin_job_list');

            } catch (\Exception $e) {
                $data['errorMessage'] = $e->getMessage();
            }
        }

        $data['form'] = $form->createView();

        return $this->render('YarshaAdminBundle:Job:add.html.twig', $data);
    }


    /**
     * @Route("/job/{id}/detail",name="yarsha_admin_job_detail")
     */
    public function viewAction(Request $request)
    {
        $breadcrumb = $this->get('apy_breadcrumb_trail');

        $data['job'] = $job = $this->get('yarsha.service.job')->getJobById($request->get('id'));

        if (!$job) {
            throw new NotFoundHttpException;
        }

        if ($organizationId = $request->get('ref')) {
            $organization = $this->get('yarsha.service.organization')->getOrganizationById($organizationId);
            if ($organization and $job->getOrganization() === $organization) {
                $data['organization'] = $organization;
                $breadcrumb->add($organization->getName(), 'yarsha_admin_organization_detail',
                    ['id' => $organizationId]);
                $breadcrumb->add("Posted Jobs", 'yarsha_admin_organization_job_list', ['id' => $organizationId]);
            }
        } else {
            $breadcrumb->add("Jobs", 'yarsha_admin_job_list');
        }

        $breadcrumb->add(substr($job->getTitle(), 0, 10) . '..');

        return $this->render('YarshaAdminBundle:Job:view.html.twig', $data);
    }


    /**
     *
     * @Route("/job/{id}/change-status",name="yarsha_admin_approve_job")
     */
    public function approveAction(Request $request)
    {
        $jobId = $request->get('id');
        $jobService = $this->get('yarsha.service.job');
        $job = $jobService->getJobById($jobId);

        if ($job) {
            $org = $job->getUserId();
            $employer = $jobService->getEmployerById($org);
            $job = $jobService->changeStatus($job->getStatus(), $jobId);

//            $eventDispatcher = $this->get('event_dispatcher');
//            $event = new EmployerJobsEmailEvent($employer, $job);
//            if ($job->getStatus() == JobConstants::JOB_STATUS_APPROVED) {
//                $eventDispatcher->dispatch(YarshaEmployerEvents::EMPLOYER_JOB_APPROVED, $event);
//            } elseif ($job->getStatus() == JobConstants::JOB_STATUS_DISABLED) {
//                $eventDispatcher->dispatch(YarshaEmployerEvents::EMPLOYER_JOB_DISABLED, $event);
//            }

            $this->addFlash('success', 'Updated successfully');

            return $this->redirectToRoute('yarsha_admin_job_list');

        } else {
            $this->addFlash('error', 'Something wrong');

            return $this->redirectToRoute('yarsha_admin_job_list');
        }


    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/job/{id}/delete", name="yarsha_admin_job_delete")
     */
    public function deleteAction(Request $request)
    {

        $jobId = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $jobRepo = $em->getRepository('YarshaJobsBundle:Job');
        $job = $jobRepo->find($jobId);
        $jobService = $this->get('yarsha.service.job');
        $response = [];
        if ($job) {
            $org = $job->getUserId();
            $employer = $jobService->getEmployerById($org);
            $job->deActivate();
            $this->getDoctrine()->getManager()->persist($job);
            try {
                $this->getDoctrine()->getManager()->flush();
                $eventDispatcher = $this->get('event_dispatcher');
                $event = new EmployerJobsEmailEvent($employer, $job);
                $eventDispatcher->dispatch(YarshaEmployerEvents::EMPLOYER_JOB_DELETED, $event);
                $response['status'] = 'success';
            } catch (\Exception $e) {
                $response['status'] = $e->getMessage();
            }
        }

        return new JsonResponse($response);
    }


    /**
     * @param Request $request
     * @return Response
     * @Route("/trashed/jobs", name="yarsha_admin_trashed_job_list")
     */
    public function listTrashsedJobsAction(Request $request)
    {
        $filters = $request->query->all();
        $trashedjobs = $this->get('doctrine.orm.entity_manager')->getRepository(Job::class)->getTrashedJobs($filters);
        $data['trashedjobs'] = $trashedjobs;

        return $this->render('@YarshaAdmin/Job/trashedJobs.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/ajax/restore/{id}/job", name="yarsha_admin_ajax_restore_job")
     */
    public function restoreTrashedJobAction(Request $request)
    {
        $data['success'] = false;
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository(Job::class)->find($id);
        if ($job) {
            $job->setStatus(JobConstants::JOB_STATUS_DISABLED);
            $em->persist($job);
            try {
                $em->flush();
                $data['message'] = "Job successfully restored.";
                $data['success'] = true;
            } catch (\Exception $e) {
                $data['errorMessage'] = "Something went wrong. Unable to restore employee.";
            }
        } else {
            $data['errorMessage'] = "Job does not exist.";
        }

        return new JsonResponse($data);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/ajax/delete/{id}/job/permanently", name="yarsha_admin_ajax_delete_job_permenently")
     */
    public function deleteJobPermanently(Request $request)
    {
        $id = $request->get('id');
        $data['success'] = false;
        $em = $this->get('doctrine.orm.default_entity_manager');
        $job = $em->getRepository(Job::class)->find($id);

        if ($job) {
            try {

                $sett = $em->getRepository(JobSetting::class)->findBy(['job' => $job]);
                $this->removeElements($sett);


                $appliedJobs = $em->getRepository(EmployeeAppliedJob::class)->getJobsApplied($job);
                $this->removeElements($appliedJobs);


                $detachedEntity = $em->merge($job);
                $em->remove($detachedEntity);
                $em->flush();

                $data['success'] = true;
                $data['message'] = "Job deleted permanently.";
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


}
