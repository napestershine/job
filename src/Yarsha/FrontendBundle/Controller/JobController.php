<?php

namespace Yarsha\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\FrontendBundle\Form\JobSearchType;
use Yarsha\JobsBundle\JobConstants;
use Yarsha\OrganizationBundle\Entity\OrganizationBannerImages;

/**
 * Class JobController
 * @package Yarsha\FrontendBundle\Controller
 */
class JobController extends Controller
{

    /**
     * @param Request $request
     * @Route("/searchjob", name="yarsha_frontend_jobs_search")
     */
    public function searchJobAction(Request $request)
    {
        $filters = $request->query->all();
        $jobs = $this->get('yarsha.service.job')->getPaginatedSearchQuery($filters);
        $data['jobs'] = $jobs;

        return $this->render("YarshaFrontendBundle:Home:jobsearch.html.twig", $data);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/jobs/list", name="yarsha_frontend_job_list")
     */
    public function browseAllJobsAction()
    {
        $data['jobs'] = $this->get('yarsha.service.job')->getPaginatedJobList();

        return $this->render('YarshaFrontendBundle:job:list.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/job/detail/{slug}", name="yarsha_frontend_job_detail_view")
     */
    public function viewJobDetailAction(Request $request)
    {
        $user = $this->getUser();
        $jobService = $this->get('yarsha.service.job');
        $job = $jobService->getJobBySlug($request->get('slug'));

        if ($user) {
            $data['appliedJob'] = $jobService->checkJobApplied($user, $job);
        } else {
            $data['appliedJob'] = '';
        }

        if (!$job) {
            throw new NotFoundHttpException();
        }
        $em = $this->getDoctrine()->getManager();
        $id = $job->getUserId();
        $employerService = $this->get('yarsha.service.employer');
        $employer = $employerService->getEmployerById($id);
        $org = $job->getOrganization();
        if ($org) {
            $org->increaseVisitCount();
            $em->persist($org);
        }
        $job->increaseViewCount();
        $em->persist($job);
        try {
            $em->flush();
        } catch (\Exception $e) {

        }

        $data['job'] = $job;
        $data['employer'] = $employer;
        $data['otherJobs'] = $jobService->getJobsByOrganization($org, 10, $job);
        $bannerImage = $this->get('doctrine.orm.entity_manager')->getRepository(OrganizationBannerImages::class)->findOneBy(
            [
                'employer' => $job->getOrganization(),
                'isFeatured' => true
            ]
        );

        $data['title'] = $job->getOrganization() . ' | Position: ' . $job->getTitle();
        $data['keywords'] = $job->getTitle();
        $data['description'] = $job->getDescription();
        $data['ogImages'] = $bannerImage;

        return $this->render('YarshaFrontendBundle:job:jobdetail.html.twig', $data);
    }

    /**
     * @param Request $request
     * @Route("/governmentjobs/search", name="yarsha_frontend_government_jobs_search")
     */
    public function governmentJobsView(Request $request)
    {
        $filters = $request->query->all();
        $filters['jobsFrom'] = JobConstants::JOB_FROM_GOVERNMENT;
        $data['jobs'] = $this->get('yarsha.service.job')->getPaginatedSearchQuery($filters);

        return $this->render("YarshaFrontendBundle:Home:jobsearch.html.twig", $data);
    }


    /**
     * @param $filename
     * @param Request $request
     * @Route("/file/download", name="yarsha_job_file_download")
     */
    public function downloadJobFileAction()
    {
        $user = $this->getUser();
        $filename = $user->getCurriculumVitaePath();
        $path = __DIR__ . '/../../../../web/uploads/jobsfile/';
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


}
