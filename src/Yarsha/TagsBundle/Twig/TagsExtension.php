<?php

namespace Yarsha\TagsBundle\Twig;

use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\TagsBundle\Service\TagService;

/**
 * Class TagsExtension
 * @package Yarsha\TagsBundle\Twig
 *
 * @DI\Service("yarsha.twig_extension.tags", public=false)
 * @DI\Tag(name="twig.extension")
 */
class TagsExtension extends \Twig_Extension
{
    /**
     * @var TagService
     */
    private $tagService;

    /**
     * TagsExtension constructor.
     *
     * @DI\InjectParams({
     *      "tagService" = @DI\Inject("yarsha.service.tags")
     * })
     *
     * @param TagService $tagService
     */
    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                    'ys_frontend_tags',
                    [$this, 'renderTags'],
                    ['is_safe' => ['html']]
                ),
            new \Twig_SimpleFunction(
                'ys_display_tags',
                [$this, 'displayTags'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'ys_render_post_by_tag_desc',
                [$this, 'postsByTagsDesc'],
                ['is_safe' => ['html']]
            )
        ];
    }


    public function displayTags($entity)
    {
        $html = '';

        $tags = $this->tagService->getTags($entity);

        if( count($tags) )
        {
            foreach($tags as $tag )
            {
                $html .= "<span class=\"label label-default\">{$tag->getName()}</span> ";
            }
        }

        return $html;
    }

    public function renderTags($entity)
    {

        $html = '';

        $tags = $this->tagService->getTags($entity);

        if( count($tags) )
        {
            $html .= "<ul class=\"es-tags\">";
            $html .= "<li>Tags: </li>";
            foreach($tags as $tag )
            {
//                $url = $this->router->generate('yarsha_frontend_search_by_tag', ['slug'=>$tag->getSlug()]);
                $url = '#';
                $html .= "<li> <a href=\"{$url}\"> {$tag->getName()} </a> </li>";
            }

            $html .= "</ul>";
        }

        return $html;
    }

    public function postsByTagsDesc($tagDescription)
    {

        $entity = $this->tagService->getEntityByTagDescription($tagDescription);

//        $routeName = ;


        $html = "<li>";
        $html .= "<article class=\"es-list-type big-thumb\">";
        $html .= "<figure class=\"thumb\">";
        $html .= ""; // image
        $html .= "</figure>";
        $html .= "<div class=\"content\">";
        $html .= "<h2>";
        $html .= $entity->getTitle(); // title
        $html .= "</h2>";
        $html .= "<time class=\"info\">";
        $html .= ""; //time
        $html .= "</time>";
        $html .= "<p>";
        $html .= ""; // post detail
        $html .= "...</p>";
        $html .= "</div>";
        $html .= "</article>";
        $html .= "</li>";

        return $html;



//                        <a href="{{ path('yarsha_frontend_' ~ post.type|lower ~ '_show', {'slug':post.slug}) }}">
//                            {{ es_load_image(post.listingImage, 'thumb_medium') }}
//                        </a>



//                            <a href="{{ path('yarsha_frontend_' ~ post.type|lower ~ '_show', {'slug':post.slug}) }}">
//                                {{ post.title }}
//                            </a>


        //{{ post.postedDate|date('M d, Y') }}

//                        {{ post.detail|striptags|slice(0, 250) }}



    }

    public function getName()
    {
        return 'tag_twig_extension';
    }


}
