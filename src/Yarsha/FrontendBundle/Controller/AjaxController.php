<?php

namespace Yarsha\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\JobsBundle\JobConstants;

class AjaxController extends Controller
{

    /**
     * @return JsonResponse
     * @Route("/ajax/hotjobs/render",name="yarsha_frontend_ajax_hotjobs_render")
     */
    public function renderHotJobs()
    {
        $em = $this->getDoctrine()->getManager();
        $jobs = $em->getRepository(Job::class)->getJobsByType(JobConstants::JOBS_TYPE_HOT);
        $results = [];

        foreach ($jobs as $job) {
            $results[$job->getOrganization()->getId()]['organization'] = $job->getOrganization();
            $results[$job->getOrganization()->getId()]['jobs'][] = $job;
        }
        $data['template'] = $this->renderView('@YarshaFrontend/Includes/hotjobs.html.twig', [
            'organizations' => $results,
        ]);

        return new JsonResponse($data);
    }

    /**
     * @return JsonResponse
     * @Route("/ajax/recentjobs/render",name="yarsha_frontend_ajax_render_recent_jobs")
     */
    public function renderRecentJobs()
    {
        $em = $this->getDoctrine()->getManager();
        $jobRepo = $em->getRepository(Job::class);
        $recentJobs = $jobRepo->getJobsByType();
        try {
            $data['template'] = $this->renderView('@YarshaFrontend/Includes/recentjobs.html.twig', [
                'recentJobs' => $recentJobs,
            ]);
        } catch (\Exception $e) {
            $data['template'] = $e->getMessage();
        }

        return new JsonResponse($this->convertFromLatin1ToUtf8Recursively($data));
    }

    /**
     * @return JsonResponse
     * @Route("/ajax/featuredjobs/render",name="yarsha_frontend_ajax_render_featured_jobs")
     */
    public function renderFeaturedJobs()
    {
        $em = $this->getDoctrine()->getManager();
        $featuredJobs = $em->getRepository(Job::class)
            ->getJobsByType(JobConstants::JOBS_TYPE_FEATURED);
        $results = [];
        foreach ($featuredJobs as $fj) {
            $results[$fj->getOrganization()->getId()]['organization'] = $fj->getOrganization();
            $results[$fj->getOrganization()->getId()]['jobs'][] = $fj;
        }
        $data['template'] = $this->renderView('@YarshaFrontend/Includes/featuredjobs.html.twig', [
            'featuredJobs' => $results,
        ]);

        return new JsonResponse($data);
    }

    /**
     * @return JsonResponse
     * @Route("/ajax/newspaperjobs/render",name="yarsha_frontend_ajax_render_newspaper_jobs")
     */
    public function renderNewspaperJobs()
    {
        $em = $this->getDoctrine()->getManager();
        $jobs = $em->getRepository(Job::class)->getJobsByType(JobConstants::JOBS_TYPE_NEWSPAPER);
        $results = [];

        foreach ($jobs as $job) {
            $results[$job->getOrganization()->getId()]['organization'] = $job->getOrganization();
            $results[$job->getOrganization()->getId()]['jobs'][] = $job;
        }
        $data['template'] = $this->renderView('@YarshaFrontend/Includes/newspaperjobs.html.twig', [
            'organizations' => $results,
        ]);

        return new JsonResponse($data);
    }

    /**
     * @return JsonResponse
     * @Route("/ajax/loginform/render", name="yarsha_frontend_ajax_render_login_form")
     */
    function renderSeekerLogin()
    {
//        $form = $this->createFormBuilder();
//        $form->add('username', TextType::class,[
//            'required' => true,
//            'attr' => [
//                'class' => 'form-control',
//                'name' => '_username'
//            ]
//        ])
//            ->add('password',PasswordType::class,[
//                'required' => true,
//                'attr' => [
//                    'class' => 'form-control',
//                    'name' => '_password'
//                ]
//            ])
//            ->add('Login',SubmitType::class,[
//                'attr' => [
//                    'class' => 'btn btn-primary'
//                ]
//            ])
//        ;
//        $data['template'] = $this->renderView('@YarshaJobSeeker/ajax/seeker_login_form.html.twig',['form' => $form->getForm()->createView()]);
        $data['template'] = $this->renderView('@YarshaJobSeeker/ajax/seeker_login_form.html.twig');

        return new JsonResponse($data);
    }

    /**
     * Encode array from latin1 to utf8 recursively
     * @param $dat
     * @return array|string
     */
    public static function convertFromLatin1ToUtf8Recursively($dat)
    {
        if (is_string($dat)) {
            return utf8_encode($dat);
        }
        if (!is_array($dat)) {
            return $dat;
        }
        $ret = [];
        foreach ($dat as $i => $d) {
            $ret[$i] = self::convertFromLatin1ToUtf8Recursively($d);
        }

        return $ret;
    }

}
