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
use Yarsha\EmployerBundle\Block\JobSeekerBlockService;
use Yarsha\JobSeekerBundle\Entity\User;
use Yarsha\JobSeekerBundle\Service\JobSeekerService;
use Yarsha\OrganizationBundle\Service\OrganizationService;

/**
 * Class CvBlockService
 * @package Yarsha\FrontendBundle\Block
 *
 * @DI\Service("yarsha.block.search_cv")
 * @DI\Tag(name="sonata.block")
 */
class CvBlockService extends AbstractBlockService
{

    private $em;
    private $seekerService;

    /**
     * HiringCompanyBlockService constructor.
     * @param EngineInterface $templating
     * @param EntityManager $em
     *
     * @DI\InjectParams({
     *     "templating" = @DI\Inject("templating"),
     *     "em" = @DI\Inject("doctrine.orm.entity_manager"),
     *     "seekerService" = @DI\Inject("yarsha.service.job_seeker")
     * })
     */

    public function __construct(EngineInterface $templating, EntityManager $em, JobSeekerService $seekerService)
    {
        parent::__construct('yarsha.block.article.category', $templating);

        $this->em = $em;
        $this->seekerService = $seekerService;
    }

    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'title' => 'CV Search',
            'template' => 'YarshaFrontendBundle:Block:cv_search.html.twig',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();


        $jobSeekers = $this->seekerService->getSearcheableSeeker();

        return $this->renderResponse(
            $blockContext->getTemplate(),
            [
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
                'jobSeekers' => $jobSeekers,
            ],
            $response
        );
    }

}
