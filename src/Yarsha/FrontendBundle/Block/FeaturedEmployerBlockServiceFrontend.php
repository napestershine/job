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
use Yarsha\OrganizationBundle\Service\OrganizationService;


/**
 * Class FeaturedEmployerBlockServiceFrontend
 * @package Yarsha\FrontendBundle\Block
 *
 * @DI\Service("yarsha.block.featured_employer.job.frontend")
 * @DI\Tag(name="sonata.block")
 */
class FeaturedEmployerBlockServiceFrontend extends AbstractBlockService
{


    private $em;

    private $employerService;

    /**
     * FeaturedEmployerBlockServiceFrontend constructor.
     * @param EngineInterface $templating
     * @param EntityManager $em
     *
     * @DI\InjectParams({
     *     "templating" = @DI\Inject("templating"),
     *     "em" = @DI\Inject("doctrine.orm.entity_manager"),
     *      "employerService" = @DI\Inject("yarsha.service.organization")
     * })
     */

    public function __construct(EngineInterface $templating, EntityManager $em, OrganizationService $employerService)
    {
        parent::__construct('yarsha.block.article.category', $templating);

        $this->em = $em;
        $this->employerService = $employerService;
    }

    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'title' => 'jobs',
            'template' => 'YarshaFrontendBundle:Includes:allFeaturedemployersblock.html.twig',
            'limit' => 10,
            'additionalClass' => ''
        ]);
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();
        $featuredEmployers = $this->employerService->getFeaturedEmployers();

        return $this->renderResponse(
            $blockContext->getTemplate(),
            [
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
                'featuredEmployers' => $featuredEmployers

            ],
            $response
        );
    }

}
