<?php

namespace Yarsha\OrganizationBundle\Block;


use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\OrganizationBundle\Service\OrganizationService;


/**
 * Class SuperEmployersBlockService
 * @package Yarsha\OrganizationBundle\Block
 *
 * @DI\Service("yarsha.block.super_employer")
 * @DI\Tag(name="sonata.block")
 */
class SuperEmployersBlockService extends AbstractBlockService
{

    /**
     * @var OrganizationService
     */
    private $employerService;

    /**
     * SuperEmployersBlockService constructor.
     * @param EngineInterface $templating
     * @param OrganizationService $organizationService
     *
     * @DI\InjectParams({
     *     "templating" = @DI\Inject("templating"),
     *     "organizationService" = @DI\Inject("yarsha.service.organization")
     * })
     */
    public function __construct(EngineInterface $templating, OrganizationService $organizationService)
    {
        parent::__construct('yarsha.block.super_employer', $templating);

        $this->employerService = $organizationService;
    }

    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'title' => 'Super Employers',
            'template' => 'YarshaOrganizationBundle:Block:super_employers.html.twig',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();

        $employers = $this->employerService->getSuperEmployers();

        $count = 1;

        $employersArray = [];

        $items = [];

        if (count($employers)) {
            foreach ($employers as $employer) {
                $items[] = $employer;

                if ($count % 4 == 0) {
                    $employersArray[] = $items;
                    $items = [];
                }

                $count++;
            }

            if (count($items)) {
                $employersArray[] = $items;
            }

        }

        return $this->renderResponse(
            $blockContext->getTemplate(),
            [
                'employers' => $employersArray,
                'block' => $blockContext->getBlock(),
                'settings' => $settings
            ],
            $response
        );
    }

}
