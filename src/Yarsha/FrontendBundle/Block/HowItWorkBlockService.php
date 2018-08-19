<?php

namespace Yarsha\FrontendBundle\Block;


use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\OrganizationBundle\Service\OrganizationService;


/**
 * Class HowItWorkBlockService
 * @package Yarsha\OrganizationBundle\Block
 *
 * @DI\Service("yarsha.block.how_it_work")
 * @DI\Tag(name="sonata.block")
 */
class HowItWorkBlockService extends AbstractBlockService
{


    /**
     * HowItWorkBlockService constructor.
     * @param EngineInterface $templating
     *
     * @DI\InjectParams({
     *     "templating" = @DI\Inject("templating")
     * })
     */
    public function __construct(EngineInterface $templating)
    {
        parent::__construct('yarsha.block.how_it_work', $templating);

    }

    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'title' => 'How It Work',
            'template' => 'YarshaFrontendBundle:Block:how_it_work.html.twig',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();


        return $this->renderResponse(
            $blockContext->getTemplate(),
            [
                'block' => $blockContext->getBlock(),
                'settings' => $settings
            ],
            $response
        );
    }

}
