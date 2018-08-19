<?php

namespace Yarsha\MainBundle\Twig;

use Yarsha\MainBundle\Entity\Category;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Request;
use Yarsha\MainBundle\Service\CategoryService;

/**
 * Class TemplateExtension
 * @package Yarsha\MainBundle\Twig
 *
 * @DI\Service("yarsha.twig.template_extension", public=false)
 * @DI\Tag(name="twig.extension")
 */
class TemplateExtension extends \Twig_Extension
{
    /**
     * @var CategoryService
     */
    private $categoryService;

    /**
     * TemplateExtension constructor.
     * @param CategoryService $categoryService
     *
     * @DI\InjectParams({
     *     "categoryService" = @DI\Inject("yarsha.service.job_category")
     *     })
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                    'ys_render_tags',
                    [$this, 'renderTags'],
                    ['is_safe' => ['html']]
                ),
            new \Twig_SimpleFunction(
                'ys_render_select_types',
                [$this, 'renderSelectElementForPostType'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'ys_render_select_status',
                [$this, 'renderSelectElementForPostStatus'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'ys_show_filter',
                [$this, 'showFilterOnRefresh'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'ys_content_detail_route_param_type',
                [$this, 'getDetailRouteType'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction('ys_no_contents',
                array($this, 'noContents'),
                array('is_safe' => array('html'))
            ),
            new \Twig_SimpleFunction('render_category_select_by_industry',
                array($this, 'renderCategorySelectByIndustry'),
                array('is_safe' => array('html'))
            ),
            new \Twig_SimpleFunction('render_category_select_by_function',
                array($this, 'renderCategorySelectByFunction'),
                array('is_safe' => array('html'))
            ),
            new \Twig_SimpleFunction('render_show_filter_button',
                array($this, 'renderShowFilterButton'),
                array('is_safe' => array('html'))
            ),
            new \Twig_SimpleFunction('is_file_exists',
                array($this, 'isFileExists'),
                array('is_safe' => array('html'))
            ),
        ];
    }

    public function isFileExists($path = "")
    {
        return $path == "" ? false : file_exists($path);
    }

    public function renderShowFilterButton()
    {
        return "<button class=\"btn btn-xs btn-default margin-r-5\" title=\"filter\" data-toggle=\"collapse\" data-target=\"#filter\">
        <i class=\"fa fa-filter\"></i>
    </button>";
    }

    public function renderTags($tags = '')
    {
        $html = '';

        if( $tags != '' )
        {
            foreach(explode(",", $tags) as $tag )
            {
                $html .= "<span class=\"label label-default\">{$tag}</span> ";
            }
        }

        return $html;
    }

    public function renderSelectElementForPostType($name, $selected = '', $extra = '')
    {
        $html = "<select name=\"{$name}\" {$extra} >";
        $html .= "<option value=\"\"> -- Select Post Type -- </option>";

//        foreach(ESConstant::$articleTypes as $k => $v)
//        {
//            $markAsSelected = ( $selected == $k ) ? 'selected="selected"' : '';
//            $html .= "<option value=\"{$k}\" {$markAsSelected} > {$v} </option>";
//        }

        $html .= '</select>';

        return $html;
    }

    public function renderSelectElementForPostStatus($name, $selected = '', $extra = '', $deleted = true)
    {
        $html = "<select name=\"{$name}\" {$extra} >";
        $html .= "<option value=\"\"> -- Select Status -- </option>";

//        foreach(ESConstant::$statusDescriptions as $k => $v)
//        {
//            if( ! $deleted and $k == ESConstant::STATUS_DELETED ) continue;
//            $markAsSelected = ( $selected == $k ) ? 'selected="selected"' : '';
//            $html .= "<option value=\"{$k}\" {$markAsSelected} > {$v['label']} </option>";
//        }

        $html .= '</select>';

        return $html;
    }

    public function showFilterOnRefresh(Request $request)
    {
        $query = $request->query->all();

        if( count($query) )
        {
            unset($query['page']);
            unset($query['limit']);
            unset($query['sort']);
            unset($query['order']);

            if( count($query) )
            {
                return true;
            }

        }

        return false;
    }

    public function getDetailRouteType($type)
    {
//        $detailRoutingMapping = array_flip(ESConstant::$detailRouteTypeMapping);
//        return (isset($detailRoutingMapping[$type]))
//            ? $detailRoutingMapping[$type]
//            : $detailRoutingMapping[ESConstant::POST_TYPE_NEWS];
        return '';


    }

    public function noContents($message = '')
    {
        $message = $message == '' ? 'No Contents Added.' : $message;
        return "<div class=\"alert text-danger\"><i class=\"fa fa-exclamation-triangle\"></i> {$message}</div>";
    }

    public function getName()
    {
        return 'template_twig_extension';
    }

    public function renderCategorySelectByIndustry($selected = null)
    {
        $html = "<select class='form-control' name='industry'>";
        $html .= "<option value=''>-- select industry type --</option>";

        $categories = $this->categoryService->getCategoriesForSelect(Category::CATEGORY_TYPE_JOB_BY_INDUSTRY);

        foreach($categories as $category)
        {
            $selectedAttr = ($selected == $category['id']) ? 'selected="selected"' : '';
            $html .= "<option value='{$category['id']}' {$selectedAttr} >{$category['title']}</option>";
        }

        $html .= "</select>";

        return $html;
    }

    public function renderCategorySelectByFunction($selected = null)
    {
        $html = "<select class='form-control' name='function'>";
        $html .= "<option value=''>-- select Category --</option>";

        $categories = $this->categoryService->getCategoriesForSelect(Category::CATEGORY_TYPE_JOB_BY_FUNCTION);

        foreach($categories as $category)
        {
            $selectedAttr = ($selected == $category['id']) ? 'selected="selected"' : '';
            $html .= "<option value='{$category['id']}' {$selectedAttr} >{$category['title']}</option>";
        }

        $html .= "</select>";

        return $html;
    }

}
