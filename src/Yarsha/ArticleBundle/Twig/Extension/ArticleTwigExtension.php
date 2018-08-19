<?php

namespace Yarsha\ArticleBundle\Twig\Extension;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Yarsha\AdminBundle\Entity\Advertisement;
use Yarsha\ArticleBundle\Entity\Article;
use Yarsha\EmployerBundle\Entity\User as Employer;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\JobsBundle\Entity\JobLevel;
use Yarsha\JobsBundle\JobConstants;
use Yarsha\MainBundle\Entity\Category;
use Yarsha\MainBundle\Entity\EducationDegree;
use Yarsha\MainBundle\Entity\Location;
use Yarsha\OrganizationBundle\Entity\Organization;
use Symfony\Component\Asset\Packages;

/**
 * Class ArticleTwigExtension
 * @package Yarsha\ArticleBundle\Twig
 *
 * @DI\Service("yarsha.twig.article")
 * @DI\Tag(name="twig.extension")
 */
class ArticleTwigExtension extends \Twig_Extension
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var
     */
    private $assetsHelper;


    /**
     * FrontEndHelper constructor.
     * @param EntityManager $em
     * @param Router $router
     *
     * @DI\InjectParams({
     *      "em"=@DI\Inject("doctrine.orm.entity_manager"),
     *      "router" = @DI\Inject("router"),
     *     "assetsHelper" = @DI\Inject("assets.packages")
     * })
     */
    public function __construct(
        EntityManager $em,
        Router $router,
        Packages $assetsHelper
    ) {
        $this->em = $em;
        $this->router = $router;
        $this->assetsHelper = $assetsHelper;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('article_detail_url', [$this, 'getDetailUrlForArticle']),
            new \Twig_SimpleFunction('render_article_category', [$this, 'renderArticleCategory']),
            new \Twig_SimpleFunction('render_article_category_list', [$this, 'renderCategorySelectByArticle'],
                ['is_safe' => ['html']]),

        ];
    }

    public function getDetailUrlForArticle(Article $article)
    {
        $param['slug'] = $article->getSlug();
        $category = $article->getCategory();
        $param['category'] = Article::$articlesCategoryDescForUrl[$category];

        return $this->router->generate('yarsha_frontend_article_detail', $param);
    }


    public function renderArticleCategory($category)
    {
        switch ($category) {
            case Article::ARTICLE_CATEGORY_ALL:
                $value = 'All';
                break;
            case Article::ARTICLE_CATEGORY_CMS_PAGE:
                $value = 'CMS Page';
                break;
            case Article::ARTICLE_CATEGORY_EMPLOYER_SERVICES:
                $value = 'Employer Services';
                break;
            case Article::ARTICLE_CATEGORY_EMPLOYEE_SERVICES:
                $value = 'Employee Services';
                break;
            case Article::ARTICLE_CATEGORY_HR_ISSUES:
                $value = 'HR Issues';
                break;
            case Article::ARTICLE_CATEGORY_CAREER_RESOURCES:
                $value = 'Career Resources';
                break;
            case Article::ARTICLE_CATEGORY_TRAINING:
                $value = 'Training';
                break;
            case Article::ARTICLE_CATEGORY_CORPORATE_SERVICES:
                $value = 'Corporate Services';
                break;
            case Article::ARTICLE_CATEGORY_PAGE_BLOCK:
                $value = 'Page Block';
                break;
            case Article::ARTICLE_CATEGORY_LOKSEWA:
                $value = 'Loksewa';
                break;
            case Article::ARTICLE_CATEGORY_NEWS:
                $value = 'News';
                break;
        }

        return $value;

    }

    public function renderCategorySelectByArticle($selected = null)
    {

        $options = '<option value="">-- Select Category --</option>';

        $categories = Article::$articleCategories;

        foreach ($categories as $k => $v) {
            $selectedAttr = $selected == $k ? 'selected="selected"' : '';
            $options .= "<option value=\"{$k}\" {$selectedAttr}>{$v}</option>";
        }

        $html = '<select name="category">';

        $html .= $options;
        $html .= "</select>";

        return $html;


    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'article_twig_extension';
    }
}
