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

/**
 * Class GovernmentJobBlockService
 * @package Yarsha\FrontendBundle\Block
 *
 * @DI\Service("yarsha.block.government.job")
 * @DI\Tag(name="sonata.block")
 */
class GovernmentJobBlockService extends AbstractBlockService
{


    private $em;

    /**
     * GovernmentJobBlockService constructor.
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
        parent::__construct('yarsha.block.government.job', $templating);

        $this->em = $em;
    }

    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'title' => 'government jobs',
            'template' => 'YarshaFrontendBundle:Block:government_jobs.html.twig',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();
        $jobRepo = $this->em->getRepository(Job::class);
        $GovernmentJobs = $jobRepo->getGovermentJobsList(12);


        return $this->renderResponse(
            $blockContext->getTemplate(),
            [
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
                'jobs' => $GovernmentJobs
            ],
            $response
        );
    }

}
