<?php

namespace Yarsha\AgencyBundle\Controller;

use Doctrine\ORM\Cache\Region;
use Doctrine\ORM\EntityManager;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Yarsha\AgencyBundle\Entity\AgencyJob;
use Yarsha\AgencyBundle\Entity\User;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Request;



class ApiController extends Controller
{

    /**
     * @var EntityManager
     * @DI\Inject("doctrine.orm.entity_manager")
     */
    private $em;


    /**
     * @ApiDoc(
     *     section="Agency Job Posting Api",
     *     resource=true,
     *     description="Job Post",
     *     headers={
     *      {"name"="access-token", "dataType"="string", "required"=true, "description"="Api access token of the user."},
     *  },
     *     parameters={
     *      {"name"="job_reference", "dataType"="string", "required"=true, "description"="Job Reference"},
     *      {"name"="job_title", "dataType"="string", "required"=true, "description"="Job Title"},
     *      {"name"="job_type", "dataType"="string", "required"=false, "description"="Job Type"},
     *      {"name"="job_duration", "dataType"="string", "required"=false, "description"="Job Duration"},
     *      {"name"="job_startdate", "dataType"="string", "required"=false, "description"="Job Start Date"},
     *      {"name"="job_skills", "dataType"="text", "required"=false, "description"="Job Skills"},
     *      {"name"="job_description", "dataType"="text", "required"=false, "description"="Job Description"},
     *      {"name"="job_location", "dataType"="string", "required"=false, "description"="Job Location"},
     *      {"name"="job_industry", "dataType"="string", "required"=false, "description"="Job Industry"},
     *      {"name"="salary_currency", "dataType"="string", "required"=false, "description"="Salary Currency"},
     *      {"name"="salary_from", "dataType"="string", "required"=false, "description"="Salary From"},
     *      {"name"="salary_to", "dataType"="string", "required"=false, "description"="Salary To"},
     *      {"name"="salary_per", "dataType"="string", "required"=false, "description"="Salary Per(year, annum, month)"},
     *      {"name"="salary_benefits", "dataType"="string", "required"=false, "description"="Salary Benefits"},
     *      {"name"="salary", "dataType"="string", "required"=false, "description"="salary"}
     *
     * }
     *)
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/agent/job-post", name="yarsha_api_agency_job_post")
     * @Method({"POST"})
     */
    public function jobPostAction(Request $request)
    {
        $user = $this->validateUser($request);

        if (!$user instanceof User) {
            return $user;
        }

        $jobReference = (string)$request->get('job_reference');
        $jobTitle = (string)$request->get('job_title');

        $jobType = (string)$request->get('job_type');
        $jobDuration = (string)$request->get('job_duration');
        $jobstartDate = (string)$request->get('job_startdate');
        $jobSkills = (string)$request->get('job_skills');

        $jobDescription = (string)$request->get('job_description');
        $jobLocation = (string)$request->get('job_location');
        $jobIndustry = (string)$request->get('job_industry');
        $salaryCurrency = (string)$request->get('salary_currency');
        $salaryFrom = (string)$request->get('salary_from');
        $salaryTo = (string)$request->get('salary_to');
        $salaryPer = (string)$request->get('salary_per');
        $salaryBenifits = (string)$request->get('salary_benefits');
        $salary = (string)$request->get('salary');

        if ($jobTitle) {
            $em = $this->em;
            $agencyJob = new AgencyJob();

            $agencyJob->setJobReference($jobReference);
            $agencyJob->setJobTitle($jobTitle);
            $agencyJob->setJobType($jobType);
            $agencyJob->setJobReference($jobReference);
            $agencyJob->setJobDuration($jobDuration);
            $agencyJob->setJobStartDate($jobstartDate);
            $agencyJob->setJobSkills($jobSkills);
            $agencyJob->setJobDescription($jobDescription);
            $agencyJob->setJobLocation($jobLocation);
            $agencyJob->setJobIndustry($jobIndustry);
            $agencyJob->setSalaryCurrency($salaryCurrency);
            $agencyJob->setSalaryFrom($salaryFrom);
            $agencyJob->setSalaryTo($salaryTo);
            $agencyJob->setSalaryPer($salaryPer);
            $agencyJob->setSalaryBenefits($salaryBenifits);
            $agencyJob->setSalary($salary);
            $agencyJob->setAgency($user);

                $em->persist($agencyJob);
                try {
                    $em->flush();
                    $message = [
                        'success' => true,
                        'message' => 'Job Added Successfully.',
                    ];
                } catch (\Throwable $e) {
                    $message = [
                        'success' => false,
                        'message' => $e->getMessage(),
                    ];
                }
            }
        else {
            $message = [
                'success' => false,
                'message' => 'Job Title can not be null.',
            ];
        }


        return new JsonResponse($message);
    }


    public function validateUser(Request $request)
    {
        $accessToken = $request->headers->get('access-token');
        $user = $this->em->getRepository(User::class)->findOneBy([
            'accessToken' => $accessToken,
        ]);

        if (!$accessToken or empty($accessToken)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Access token is required.',
            ]);
        }

        if (!$user instanceof User) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid access token',
            ]);
        }

        return $user;
    }



}
