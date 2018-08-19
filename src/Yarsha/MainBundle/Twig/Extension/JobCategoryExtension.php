<?php

namespace Yarsha\MainBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\JobsBundle\JobConstants;

/**
 * Class JobCategoryExtension
 * @package Yarsha\MainBundle\Twig
 *
 * @DI\Service("yarsha.twig_jobcategory")
 * @DI\Tag(name="twig.extension")
 */
class JobCategoryExtension extends \Twig_Extension
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
            new \Twig_SimpleFunction('count_jobs_by_category', [$this, 'countJobsByCategory']),
            new \Twig_SimpleFunction('count_jobs_by_industry', [$this, 'countJobsByIndustry'])
        ];
    }

    public function countJobsByCategory($id)
    {
        $jobs = [];
        $category = $this->em->getRepository("YarshaMainBundle:Category")->find($id);
        if ($category) {
            $jobs = $this->em->getRepository("YarshaJobsBundle:Job")->findBy([
                'category' => $category,
                'status' => JobConstants::JOB_STATUS_APPROVED
            ]);
        }

        return count($jobs);

    }

    public function countJobsByIndustry($id)
    {
        $jobs = [];
        $category = $this->em->getRepository("YarshaMainBundle:Category")->find($id);
        if ($category) {
            $jobs = $this->em->getRepository("YarshaJobsBundle:Job")->findBy([
                'industry' => $category,
                'status' => JobConstants::JOB_STATUS_APPROVED
            ]);
        }

        return count($jobs);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'job_category';
    }
}
