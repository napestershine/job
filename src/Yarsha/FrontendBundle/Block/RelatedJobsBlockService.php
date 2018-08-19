<?php
/**
 * Created by PhpStorm.
 * User: yarsha-mandip
 * Date: 5/1/17
 * Time: 4:31 PM
 */

namespace Yarsha\FrontendBundle\Block;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Yarsha\EmployerBundle\Service\EmployerService;
use Yarsha\JobsBundle\Entity\Job;
use Symfony\Component\HttpFoundation\Response;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Yarsha\JobsBundle\Service\JobService;
use Yarsha\OrganizationBundle\Entity\Organization;

/**
 * Class RelatedJobsBlockService
 * @package Yarsha\FrontendBundle\Block
 * @DI\Service("yarsha.block.relatedjobs")
 * @DI\Tag("sonata.block")
 */
class RelatedJobsBlockService extends AbstractBlockService
{

    /**
     * @var EntityManager
     */
    private $em;

    private $jobService;

    private $employerService;

    /**
     * JobsBlockService constructor.
     * @param EngineInterface $templating
     * @param EntityManager $em
     *
     * @DI\InjectParams({
     *     "templating" = @DI\Inject("templating"),
     *     "em" = @DI\Inject("doctrine.orm.entity_manager"),
     *     "jobService" = @DI\Inject("yarsha.service.job"),
     *     "employerService" = @DI\Inject("yarsha.service.employer")
     * })
     */

    public function __construct(
        EngineInterface $templating,
        EntityManager $em,
        JobService $jobService,
        EmployerService $employerService
    ) {
        parent::__construct('yarsha.block.relatedjobs', $templating);
        $this->em = $em;
        $this->jobService = $jobService;
        $this->employerService = $employerService;
    }

    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'title' => 'Related Jobs',
            'template' => 'YarshaFrontendBundle:Block:relatedjobs.html.twig',
            'job' => false,
            'employer' => false,
            'type'=> null,
            'organization' => null
        ]);
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $recentJobs = [];
        $settings = $blockContext->getSettings();
        if ($settings['job']) {
            $relatedJobs = $this->jobService->getRelatedJobs($settings['job'], 5);
        } else {
            $relatedJobs = $this->employerService->getJobsofEmployer($settings['employer'], 5);
        }

        $organization = $settings['organization'];
        if($type='recent_jobs' and $organization instanceof  Organization){
            $recentJobs = $this->em
                ->getRepository(Job::class)
                ->getJobsByOrganization($organization->getId(), 15);
        }

        return $this->renderResponse(
            $blockContext->getTemplate(),
            [
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
                'relatedJobs' => $relatedJobs,
                'recentJobs' => $recentJobs
            ],
            $response
        );
    }
}
