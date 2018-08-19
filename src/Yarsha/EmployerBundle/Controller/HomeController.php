<?php

namespace Yarsha\EmployerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Yarsha\EmployerBundle\Entity\User;
use Yarsha\OrganizationBundle\Entity\Organization;
use Yarsha\OrganizationBundle\Entity\OrganizationAutoEmailResponder;
use Yarsha\EmployerBundle\Form\AutoEmailResponseType;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Class HomeController
 * @package Yarsha\EmployerBundle\Controller
 * @Route("/employer")
 */
class HomeController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="yarsha_employer_homepage")
     */
    public function indexAction(Request $request)
    {

        $data = [];
        $employerId = $this->getUser()->getOrganization() instanceof Organization ? $this->getUser()->getOrganization()->getId() : '';
//        $organizationName = $this->getUser()->getOrganization()->getName();

        $data['employerId'] = $employerId;

        return $this->render('YarshaEmployerBundle:Home:index.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/auto_email_responder", name="yarsha_employer_auto_email_responder")
     * @Breadcrumb("Auto Email Responder")
     */
    public function autoEmailResponderAction(Request $request)
    {

        $data = [];
        $employerId = $this->getUser()->getOrganization()->getId();

        $em = $this->get('doctrine.orm.entity_manager');
        $emailResponse = $em->getRepository(OrganizationAutoEmailResponder::class)->findOneBy(['organizationId' => $employerId]);

        if ($emailResponse) {
            $autoresponse = $em->getRepository(OrganizationAutoEmailResponder::class)->find($emailResponse->getId());
        } else {
            $autoresponse = new OrganizationAutoEmailResponder();
            $autoresponse->setOrganizationId($employerId);
        }


        $form = $this->createForm(AutoEmailResponseType::class, $autoresponse);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {

            try {

                $autoresponse = $form->getData();
                $em = $this->get('doctrine.orm.entity_manager');
                $em->persist($autoresponse);
                $em->flush();

                $successMessage = 'Auto email response text update successfully';

                $this->addFlash('success', $successMessage);

                return $this->redirectToRoute('yarsha_employer_auto_email_responder');

            } catch (\Exception $e) {
                $data['errorMessage'] = $e->getMessage();
            }
        }

        $data['form'] = $form->createView();

        return $this->render('YarshaEmployerBundle:Employer:autoEmailResponder.html.twig', $data);

    }


    /**
     * @param Request $request
     * @Route("/search_resume", name ="yarsha_employer_resume_search")
     * @Breadcrumb("Resume")
     */
    public function searchResumeAction(Request $request)
    {
        $filters = $request->query->all();
        $employerId = $this->getUser()->getId();
        $employerService = $this->get('yarsha.service.employer');
        $searchResult = $employerService->getPaginatedsearchResume($employerId, $filters);
        $data['results'] = $searchResult;

        return $this->render("YarshaEmployerBundle:Employer:resume_search.html.twig", $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/{name}/candidates", name="yarsha_employer_shortlisted")
     * @Breadcrumb("Candidates")
     * @Breadcrumb("{name}")
     */
    public function OrganizationCandidatesAction(Request $request)
    {
        $type = $request->get('name');
        if ($type == 'save') {
            $types = 'Saved';
        } else {
            $types = $type;
        }
        $filters = [
            'type' => $type
        ];
        $employerId = $this->getUser()->getId();
        $employerService = $this->get('yarsha.service.employer');
        $results = $employerService->getPaginatedAppliedResume($employerId, $filters);
        $data['title'] = $types;
        $data['applicants'] = $results;

        return $this->render("YarshaEmployerBundle:Jobseeker:shortlisted.html.twig", $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/search/{username}/applicant-details", name="yarsha_employer_job_applicants_details_from_search")
     * @Breadcrumb("Resume",routeName="yarsha_employer_resume_search")
     */

    public function applicantDetailsFromSearchAction(Request $request)
    {

        $seekerId = $request->get('username');
        $seekerService = $this->get('yarsha.service.job_seeker');
        $seeker = $seekerService->getSeekerByUsername($seekerId);

        $seeker->increaseProfileVisits();
        $this->getDoctrine()->getManager()->persist($seeker);
        try {
            $this->getDoctrine()->getManager()->flush();
        } catch (\Exception $e) {

        }

        $followedCompanies = $this->getDoctrine()->getManager()->getRepository(User::class)->getEmployersFollowedBySeeker($seekerId);
        $education = $seekerService->getSeekerEducation($seeker);
        $data['educations'] = $education;
        $data['detail'] = $seeker;
        $data['companies'] = $followedCompanies;
        $this->get('apy_breadcrumb_trail')->add($seeker->getFirstName());

        return $this->render('@YarshaEmployer/Jobs/search-applicant-detail.html.twig', $data);
    }


}
