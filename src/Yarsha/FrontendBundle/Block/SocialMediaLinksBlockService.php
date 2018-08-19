<?php

namespace Yarsha\FrontendBundle\Block;


use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\AdminBundle\OptionsConstants;
use Yarsha\AdminBundle\Service\OptionsService;


/**
 * Class SocialMediaLinksBlockService
 * @package Yarsha\FrontendBundle\Block
 *
 * @DI\Service("yarsha.block.social_media")
 * @DI\Tag(name="sonata.block")
 */
class SocialMediaLinksBlockService extends AbstractBlockService
{

    /**
     * @var OptionsService
     */
    private $settingsService;

    /**
     * SocialMediaLinksBlockService constructor.
     * @param EngineInterface $templating
     * @param OptionsService $settingsService
     *
     * @DI\InjectParams({
     *     "templating" = @DI\Inject("templating"),
     *     "settingsService" = @DI\Inject("yarsha.service.options")
     * })
     */

    public function __construct(EngineInterface $templating, OptionsService $settingsService)
    {
        parent::__construct('yarsha.block.social_media', $templating);

        $this->settingsService = $settingsService;
    }

    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'title' => 'Social Media Links',
            'template' => 'YarshaFrontendBundle:Block:social_media_links.html.twig',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();

        $socialLinks = $this->settingsService->getOptions(OptionsConstants::OPTION_GROUPS_SOCIAL_LINK);
        $links['facebook'] = $socialLinks[OptionsConstants::SOCIAL_LINK_FACEBOOK];
        $links['twitter'] = $socialLinks[OptionsConstants::SOCIAL_LINK_TWITTER];
        $links['google_plus'] = $socialLinks[OptionsConstants::SOCIAL_LINK_GOOGLE_PLUS];
        $links['linkedin'] = $socialLinks[OptionsConstants::SOCIAL_LINK_LINKEDIN];

        return $this->renderResponse(
            $blockContext->getTemplate(),
            [
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
                'links' => $links
            ],
            $response
        );
    }

}
