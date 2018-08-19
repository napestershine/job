<?php

namespace Yarsha\JobsBundle\Twig;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\JobSeekerBundle\Entity\EmployeeAppliedJob;

/**
 * Class JobExtension
 * @package Yarsha\JobsBundle\Twig
 *
 * @DI\Service("yarsha.service.job_twig_extension", public=false)
 * @DI\Tag(name="twig.extension")
 */
class JobExtension extends \Twig_Extension
{

    private $em;

    /**
     * @DI\InjectParams({
     * "em" = @DI\Inject("doctrine.orm.entity_manager")
     * })
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'total_applicant',
                [$this, 'totalApplicant'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'jobApplied_status',
                [$this, 'jobAppliedStatus'],
                ['is_safe' => ['html']]
            )
        ];
    }


    public function totalApplicant($id)
    {

        $job = $this->em->getRepository("YarshaJobsBundle:Job")->find($id);
        if ($job) {
            $qb = $this->em->getRepository("YarshaJobsBundle:Job")->getApplicantByJob($id);
        }
        $applicants = $qb->getQuery()->getResult();

        return count($applicants);

    }

    public function jobAppliedStatus($id)
    {

        $jobApplied = $this->em->getRepository(EmployeeAppliedJob::class)->find($id);
        $jobstatus = EmployeeAppliedJob::$appliedJobStatus;
        $option = '<select class="changeStatus btn btn-xs btn-primary" data-app-id="' . $id . '">';
        foreach ($jobstatus as $key => $value) {
            $selected = '';
            if ($key == $jobApplied->getStatus()) {
                $selected = 'selected';
            }
            $option = $option . '<option ' . $selected . ' value=' . $key . '>' . $value . '</option>';
        }
        $option = $option . '</select>';

        return $option;

    }


    public function getName()
    {
        return 'job_twig_extension';
    }

}
