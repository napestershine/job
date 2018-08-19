<?php

namespace Yarsha\FrontendBundle\Block;

use Doctrine\ORM\EntityManager;
use Yarsha\AdminBundle\Entity\Advertisement;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\JobsBundle\JobConstants;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Response;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;


/**
 * Class JobsBlockService
 * @package Yarsha\FrontendBundle\Block
 *
 * @DI\Service("yarsha.block.newspaperjobs")
 * @DI\Tag(name="sonata.block")
 */
class NewspaperJobsBlockService extends AbstractBlockService
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
        parent::__construct('yarsha.block.newspaperjobs', $templating);

        $this->em = $em;
    }

    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'title' => 'Newspaper jobs',
            'template' => 'YarshaFrontendBundle:Block:newspaperjobs.html.twig',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();
        $jobs = $this->em->getRepository(Job::class)->getJobsByType(JobConstants::JOBS_TYPE_NEWSPAPER);
        $results = [];

        foreach($jobs as $job)
        {
            $results[$job->getOrganization()->getId()]['organization'] = $job->getOrganization();
            $results[$job->getOrganization()->getId()]['jobs'][] = $job;
        }
        
        return $this->renderResponse(
            $blockContext->getTemplate(),
            [
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
                'organizations' => $results,
            ],
            $response
        );
    }

}
