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
use Yarsha\ArticleBundle\Entity\Article;


/**
 * Class ArticleByCategoryBlockService
 * @package Yarsha\FrontendBundle\Block
 *
 * @DI\Service("yarsha.block.article.category")
 * @DI\Tag(name="sonata.block")
 */
class ArticleByCategoryBlockService extends AbstractBlockService
{


    private $em;

    /**
     * ArticleByCategoryBlockService constructor.
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
        parent::__construct('yarsha.block.article.category', $templating);

        $this->em = $em;
    }

    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'title' => 'jobs',
//            'template' => 'YarshaFrontendBundle:Block:article_list_home_block.html.twig',
            'template' => 'YarshaFrontendBundle:Block:article_list_inner_block.html.twig',
            'type' => 1,
            'category' => 5,
            'wrapperClass' => 'col-md-4 col-sm-6',
            'limit' => 2,
            'iconClass' => '',
            'innerPage' => false
        ]);
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();
        $jobRepo = $this->em->getRepository(Article::class);
        $articles = $jobRepo->getFrontendArticleList([
                'category' => $settings['category'],
                'limit' => $settings['limit']
            ]
        );

        $category = Article::$articlesCategoryDescForUrl[$settings['category']];

        return $this->renderResponse(
            $blockContext->getTemplate(),
            [
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
                'articles' => $articles,
                'category' => $category
            ],
            $response
        );
    }

}
