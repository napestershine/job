<?php

namespace Yarsha\EmployerBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Yarsha\EmployerBundle\Event\EmployerEvent;
use Yarsha\EmployerBundle\YarshaEmployerEvents;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\JobsBundle\JobConstants;
use Yarsha\JobSeekerBundle\Entity\EmployeeAppliedJob;
use Yarsha\JobsBundle\Form\JobType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Yarsha\MainBundle\Event\EmployerJobsEmailEvent;
use Yarsha\MainBundle\Event\JobSeekerJobsEmailEvent;
use Yarsha\MainBundle\MainBundleConstants;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yarsha\MainBundle\MainBundleEvents;
use Yarsha\OrganizationBundle\Entity\OrganizationContactPerson;
use Yarsha\OrganizationBundle\Form\OrganizationContactPersonType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Yarsha\JobSeekerBundle\Entity\User;

/**
 * Class JobsController
 * @package Yarsha\EmployerBundle\Controller
 * @Route("/employer")
 * @Breadcrumb("Jobs",routeName="yarsha_employer_job_list")
 */
class JobsController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/jobs", name="yarsha_employer_job_list")
     * @Breadcrumb("list")
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYER');

        $user = $this->getUser();

        if (!($organization = $user->getOrganization())) {
            throw new AccessDeniedException;
        }

        $filters = $request->query->all();

        $filters['organization'] = $organization->getId();

        $data['jobs'] = $this->get('yarsha.service.job')->listJobs($filters);

        return $this->render("@YarshaEmployer/Jobs/list.html.twig", $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/job/post", name="yarsha_employer_job_post")
     * @Route("/job/{id}/update", name="yarsha_employer_job_update")
     */
    public function postJob(Request $request)
    {
        $id = $request->get('id');
//        $this->denyAccessUnlessGranted('ROLE_EMPLOYER');
        $user = $this->getUser();
        $data['isUpdating'] = false;
        $eventDispatcher = $this->get('event_dispatcher');
        if ($id) {
            $job = $this->get('yarsha.service.job')->getJobById($id);
            if (
                !$job
                or $job->getJobsFrom() != JobConstants::JOB_FROM_EMPLOYERS
                or $job->getOrganization() !== $user->getOrganization()
            ) {
                throw new NotFoundHttpException;
            }
            $data['isUpdating'] = true;
            $this->get('apy_breadcrumb_trail')->add($job->getTitle());
        } else {
            $job = new Job();
            $this->get('apy_breadcrumb_trail')->add('new');
        }

        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            try {
                $job = $form->getData();
                if ($data['isUpdating'] == false) {
                    $job->setJobsFrom(JobConstants::JOB_FROM_EMPLOYERS);
                    $job->setUserId($user->getId());
                    $job->setUsername($user->getUsername());
                    $job->setOrganization($user->getOrganization());
                }
                $em = $this->get('doctrine.orm.entity_manager');
                $em->persist($job);

                $settings = $job->getSettings();
                $settings->setJob($job);
                $em->persist($settings);

                $em->flush();

                $successMessage = $data['isUpdating'] ? 'Job Updated Successfully.' : 'Job Added Successfully. Please wait for admin approval.';
//                $event = new EmployerJobsEmailEvent($user, $job);
//                if ($data['isUpdating']) {
//                    $eventDispatcher->dispatch(YarshaEmployerEvents::EMPLOYER_JOB_UPDATE, $event);
//                } else {
//                    $eventDispatcher->dispatch(YarshaEmployerEvents::EMPLOYER_JOB_POST, $event);
//                }

                $this->addFlash('success', $successMessage);

                return $this->redirectToRoute('yarsha_employer_job_list');
            } catch (\Exception $e) {
                $data['errorMessage'] = $e->getMessage();
            }
        }
        $data['form'] = $form->createView();

        return $this->render('@YarshaEmployer/Jobs/post.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/job/{id}/view", name="yarsha_employer_job_details")
     */
    public function viewAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYER');

        $jobId = $request->get('id');
        $jobService = $this->get('yarsha.service.job');
        $job = $jobService->getJobById($jobId);
        $data['job'] = $job;
        $this->get('apy_breadcrumb_trail')->add($job->getTitle());

        return $this->render("@YarshaEmployer/Jobs/view.html.twig", $data);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/job/{id}/applicant", name="yarsha_employer_job_applicants")
     */

    public function applicationAction(Request $request)
    {
        $jobService = $this->get('yarsha.service.job');
        $jobId = $request->get('id');
        $job = $jobService->getJobById($jobId);
        $applicants = $jobService->getPaginatedJobApplicant($jobId);
        $data['applicants'] = $applicants;

        $this->get('apy_breadcrumb_trail')->add($job->getTitle(), 'yarsha_employer_job_details',
            ["id" => $job->getId()]);
        $this->get('apy_breadcrumb_trail')->add('Applicant');

        return $this->render('@YarshaEmployer/Jobs/applicant.html.twig', $data);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/job/{jobId}/{id}/applicant-details", name="yarsha_employer_job_applicants_details")
     */

    public function applicantDetailsAction(Request $request)
    {
        $jobService = $this->get('yarsha.service.job');
        $jobId = $request->get('jobId');
        $job = $jobService->getJobBySlug($jobId);
        $seekerId = $request->get('id');
        $seekerService = $this->get('yarsha.service.job_seeker');
        $seeker = $seekerService->getSeekerByUsername($seekerId);

        $seeker->increaseProfileVisits();
        $this->getDoctrine()->getManager()->persist($seeker);
        try{
            $this->getDoctrine()->getManager()->flush();
        }   catch (\Exception $e){

        }

        $followedCompanies = $seekerService->getSeekerFollowedCompanies($seekerId);
        $education = $seekerService->getSeekerEducation($seeker);
        $data['educations'] = $education;
        $data['detail'] = $seeker;
        $data['companies'] = $followedCompanies;
        $this->get('apy_breadcrumb_trail')->add($job->getTitle());
        $this->get('apy_breadcrumb_trail')->add('Applicant', 'yarsha_employer_job_applicants', ['id' => $job->getId()]);
        $this->get('apy_breadcrumb_trail')->add($seeker->getFirstName());
        $data['job'] = $job;
        $data['applicant'] = true;

        return $this->render('@YarshaEmployer/Jobs/search-applicant-detail.html.twig', $data);

    }


    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/ajax/job/update/status", name="yarsha_ajax_update_jobApplied_status")
     */
    public function updateStatusAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $employeeService = $this->get('yarsha.service.job_seeker');
        $sts = $request->get('status');
        $appId = $request->get('appId');
        $job = $em->getRepository(EmployeeAppliedJob::class)->find($appId);
        $applicant = $employeeService->getSeekerById($job->getEmployee());
        if ($job) {

            $job->setStatus($sts);
            $em->persist($job);
            $eventDispatcher = $this->get('event_dispatcher');
            $event = new JobSeekerJobsEmailEvent($applicant, $job->getJob());
            if ($sts == 'selected') {
                $eventDispatcher->dispatch(MainBundleEvents::EMAIL_EVENT_JOB_SEEKER_APPLICATION_SELECTED, $event);
            } elseif ($sts == 'shortlisted') {
                $eventDispatcher->dispatch(MainBundleEvents::EMAIL_EVENT_JOB_SEEKER_APPLICATION_SHORTLISTED, $event);
            } elseif ($sts == 'rejected') {
                $eventDispatcher->dispatch(MainBundleEvents::EMAIL_EVENT_JOB_SEEKER_APPLICATION_REJECTED, $event);
            }
        }

        $response = [];
        try {
            $em->flush();
            $response['status'] = $sts;
            $response['message'] = 'success';
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }

        return new JsonResponse($response);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}/request-cv", name="yarsha_employer_request_cv")
     */
    public function requestCvAction(Request $request)
    {
        $organization = $this->getUser()->getOrganization();
        $id = $request->get('id');
        $jobid = $request->get('jobId');
        $seekerService = $this->get('yarsha.service.job_seeker');
        $seeker = $seekerService->getSeekerById($id);

        $employerContactPerson = $this->getDoctrine()->getRepository(OrganizationContactPerson::class)
            ->findOneBy([
                'organization' => $this->getUser()->getId(),
                'contactType' => OrganizationContactPerson::CONTACT_TYPE_MAIN_CONTACT
            ]);

        $url = $this->get('router')->generate(
            'yarsha_admin_seeker_detail',
            ['id' => $seeker->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $data['organization_name'] = $organization->getName();
        $data['seeker_name'] = $seeker->getFirstName() . ' ' . $seeker->getLastName();
        $data['url'] = $url;


        // Admin Email
        $toEmail = $seeker->getContactEmail() ? $seeker->getContactEmail() : 'info@kantipurjob.com';
        $subject = 'CV Request';
        $fromEmail = $employerContactPerson->getEmail();
        $body = $this->renderView(
            'Emails/cv_request.html.twig',
            $data
        );

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($body,
                'text/html'
            );
        if ($this->get('mailer')->send($message)) {
            $this->addFlash('success', 'CV Request successfully sent.');
        } else {
            $this->addFlash('errorMessage', 'Error!.');
        }

        return $this->redirectToRoute('yarsha_employer_job_applicants_details', ['jobId' => $jobid, 'id' => $id]);
    }

    /**
     * @param Request $request
     * @Route("/{id}/seekercv/download", name="yarsha_employer_cv_download")
     */
    public function downloadedGeneratedResume(Request $request){
        $id = $request->get('id');
        $seekerService = $this->get('yarsha.service.job_seeker');
        $seeker = $seekerService->getSeekerById($id);
        if(!$seeker){
            throw new NotFoundHttpException('Seeker does not exist.');
        }

        return $seekerService->downloadGeneratedResume($seeker);
    }

    public function downloadCvAction(Request $request)
    {
        $id = $request->get('id');
        $seekerService = $this->get('yarsha.service.job_seeker');
        $seeker = $seekerService->getSeekerById($id);
        $filename = $seeker->getCurriculumVitaePath();
        $path = __DIR__ . '/../../../../web/uploads/seekers/';
        $download_file = $path . $filename;
        if (file_exists($download_file)) {
            $extension = explode('.', $filename);
            $extension = $extension[count($extension) - 1];
            header('Content-Transfer-Encoding: binary');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
            header('Accept-Ranges: bytes');
            header('Content-Length: ' . filesize($download_file));
            header('Content-Encoding: none');
            header('Content-Type: application/' . $extension);
            header('Content-Disposition: attachment; filename=' . $filename);
            readfile($download_file);
            exit;
        } else {
            echo 'File does not exists on given path';
        }
        exit;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/ajax/applicant/delete", name="yarsha_employer_ajax_applicant_delete")
     */
    public function deleteApplicantAction(Request $request)
    {
        $Id = $request->get('employee');
        $em = $this->get('doctrine.orm.entity_manager');
        $applicant = $em->getRepository(EmployeeAppliedJob::class)->find($Id);
        if (!$applicant) {
            return new JsonResponse(['status' => 'error', 'message' => 'No applicant Found'], 400);
        }

        try {
            $em->remove($applicant);
            $em->flush();
            $this->addFlash('success', 'Applicant Deleted Successfully.');
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        }

        return new JsonResponse(['status' => 'success']);
    }



}
