<?php

namespace Yarsha\AgencyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AgencyController
 * @package Yarsha\AgencyBundle\Controller
 *
 * @Route("/agency")
 */

class AgencyController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="yarsha_agency_dashboard")
     */
    public function indexAction(Request $request)
    {
        return $this->render('YarshaAgencyBundle:Default:index.html.twig');

    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/jobs", name="yarsha_agency_job_list")
     */
    public function jobListAction(Request $request)
    {
        $filters['agency'] = $this->getUser();
        $agencyService = $this->get('yarsha.service.admin_agency');
        $agencies = $agencyService->getPaginatedAgencyJobList($filters);
        $data['jobs'] = $agencies;

        return $this->render('YarshaAgencyBundle:Default:joblist.html.twig', $data);

    }


}
