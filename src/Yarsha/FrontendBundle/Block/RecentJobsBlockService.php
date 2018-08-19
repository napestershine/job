<?php

namespace Yarsha\FrontendBundle\Block;

use Doctrine\ORM\EntityManager;
use Yarsha\JobsBundle\Entity\Job;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Response;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Class RecentJobsBlockService
 * @package Yarsha\FrontendBundle\Block
 *
 * @DI\Service("yarsha.block.recent_jobs")
 * @DI\Tag(name="sonata.block")
 */
class RecentJobsBlockService extends AbstractBlockService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * JobsBlockService constructor.
     * @param EngineInterface $templating
     * @param EntityManager $em
     *
     * @DI\InjectParams({
     *     "templating" = @DI\Inject("templating"),
     *     "em" = @DI\Inject("doctrine.orm.entity_manager")
     * })
     */

    public function __construct(EngineInterface $templating, EntityManager $em)
    {
        parent::__construct('yarsha.block.recent_jobs', $templating);

        $this->em = $em;
    }

    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'title' => 'jobs',
            'template' => 'YarshaFrontendBundle:Block:recent_jobs.html.twig'
        ]);
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();
        $jobRepo = $this->em->getRepository(Job::class);

        $recentJobs = $jobRepo->getJobsByType();

        return $this->renderResponse(
            $blockContext->getTemplate(),
            [
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
                'recentJobs' => $recentJobs,
            ],
            $response
        );
    }

}
