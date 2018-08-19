<?php
/**
 * Created by PhpStorm.
 * User: zone
 * Date: 1/22/17
 * Time: 3:05 PM
 */

namespace Yarsha\JobsBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Yarsha\EmployerBundle\YarshaEmployerEvents;
use Yarsha\JobsBundle\Entity\Job;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yarsha\JobsBundle\Entity\JobApplied;
use Yarsha\JobsBundle\Form\JobType;
use Yarsha\JobsBundle\JobConstants;
use Yarsha\MainBundle\Event\EmployerJobsEmailEvent;

class JobController extends Controller
{






    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/employer/job/{id}/delete", name="yarsha_employer_job_delete")
     */

    public function deleteAction(Request $request)
    {
        $jobId = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $jobRepo = $em->getRepository('YarshaJobsBundle:Job');
        $job = $jobRepo->find($jobId);
        $eventDispatcher = $this->get('event_dispatcher');
        $jobService = $this->get('yarsha.service.job');
        $response = [];
        if ($job) {
            $job->deActivate();
            $em->persist($job);
            try {
                $em->flush();
                $employer = $jobService->getEmployerByOrganization($job->getOrganization());
                $event = new EmployerJobsEmailEvent($employer, $job);
                $eventDispatcher->dispatch(YarshaEmployerEvents::EMPLOYER_JOB_DELETED,$event);
                $response['status'] = 'success';
            } catch (\Exception $e) {
                $response['status'] = $e->getMessage();
            }
        }

        return new JsonResponse($response);
    }


    /**
     * @Route("/apply-job/{id}", name="yarsha_job_seeker_job_apply" )
     */
    public function applyJobAction(Request $request)
    {
        $id = $request->get('id');

        $jobService = $this->get('yarsha.service.job');
        $job = $jobService->getJobById($id);


        $user = $this->getUser();

        $data['message'] = $jobService->saveApplyJob($job, $user, JobApplied::APPLY_ONLINE);


        return $this->redirectToRoute('yarsha_job_seeker_homepage', $data);

    }

    /**
     * @Route("job/{id}/category", name="yarsha_jobs_by_category")
     */
    public function viewJobsByCategoryAction($id)
    {
        $jobService = $this->get('yarsha.service.job');
        $jobs = $jobService->getJobsByCategory($id);

        return $this->render("YarshaJobsBundle:Jobs:jobsbycategory.html.twig", ['jobs' => $jobs]);
    }


}
