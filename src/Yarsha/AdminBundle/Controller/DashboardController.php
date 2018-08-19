<?php

namespace Yarsha\AdminBundle\Controller;

use Proxies\__CG__\Yarsha\OrganizationBundle\Entity\Organization;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Yarsha\OrganizationBundle\Entity\Organization as employer;
use Yarsha\JobsBundle\Entity\Job;

/**
 * Class DashboardController
 * @package Yarsha\AdminBundle\Controller
 *
 * @Route("/admin")
 */
class DashboardController extends Controller
{


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="yarsha_admin_dashboard")
     */
    public function indexAction(Request $request)
    {
        $data = [];
        $em = $this->get('doctrine.orm.entity_manager');
        $jobRepository = $em->getRepository(Job::class);


        $jobCounts = $jobRepository->getTodaysJobCount();
        $organizationCounts = $jobRepository->getTodaysEmployerCount();
        $seekerCounts = $jobRepository->getTodaysSeeekerCount();
        $appliedCounts = $jobRepository->getTodaysApplied();
        $phonecallCounts = $jobRepository->getTodaysPhoneCalls();

        $data['job'] = count($jobCounts);
        $data['employer'] = count($organizationCounts);
        $data['seeker'] = count($seekerCounts);
        $data['applied'] = count($appliedCounts);
        $data['phone_call'] = count($phonecallCounts);


        return $this->render('YarshaAdminBundle:Dashboard:index.html.twig', $data);
    }
}
