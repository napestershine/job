<?php

namespace Yarsha\MainBundle\Twig;

use Pagerfanta\Pagerfanta;
use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\MainBundle\Service\PaginationService;

/**
 * Class PagerExtension
 * @package Yarsha\MainBundle\Twig
 *
 * @DI\Service("yarsha.twig.pager", public=false)
 * @DI\Tag(name="twig.extension")
 */
class PagerExtension extends \Twig_Extension
{

    /**
     * @var PaginationService
     */
    private $paginationService;

    /**
     * PagerExtension constructor.
     *
     * @DI\InjectParams({
     *     "paginationService" = @DI\Inject("yarsha.service.pagination")
     * })
     *
     * @param PaginationService $paginationService
     */
    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'pager_first_index',
                [$this, 'pagerFirstIndex']
            ),
            new \Twig_SimpleFunction(
                'pager_sort',
                [$this, 'pagerSort'],
                ['is_safe' => ['html']]
            )
        ];
    }

    public function pagerFirstIndex(Pagerfanta $pager)
    {
        return (($pager->getCurrentPage() - 1) * $pager->getMaxPerPage()) + 1;
    }

    public function pagerSort($label, $route = '', $fieldName = '', $params = [])
    {
        $route = ($route != "" and $route != "#") ? $this->paginationService->getRoute($route, $fieldName,
            $params) : '#';

        return "<a href=\"{$route}\">{$label} &nbsp;<i class=\"fa fa-sort\"></i> </a>";
    }

    public function getName()
    {
        return 'pager_twig_extension';
    }

}
