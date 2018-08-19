<?php

namespace Yarsha\FrontendBundle\Block;


use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\JobsBundle\JobConstants;

/**
 * Class FeaturedJobsBlockService
 * @package Yarsha\FrontendBundle\Block
 *
 * @DI\Service("yarsha.block.featured_jobs")
 * @DI\Tag(name="sonata.block")
 */
class FeaturedJobsBlockService extends AbstractBlockService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * FeaturedJobsBlockService constructor.
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
        parent::__construct('yarsha.block.featured_jobs', $templating);

        $this->em = $em;
    }

    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'title' => 'jobs',
            'template' => 'YarshaFrontendBundle:Block:featured_jobs.html.twig',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();
        $featuredJobs = $this->em->getRepository(Job::class)->getJobsByType(JobConstants::JOBS_TYPE_FEATURED);

        $results = [];

        foreach($featuredJobs as $fj)
        {
            $results[$fj->getOrganization()->getId()]['organization'] = $fj->getOrganization();
            $results[$fj->getOrganization()->getId()]['jobs'][] = $fj;
        }

        return $this->renderResponse(
            $blockContext->getTemplate(),
            [
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
                'featuredJobs' => $results
            ],
            $response
        );
    }

}
