<?php

namespace Yarsha\MainBundle\Service;

use Pagerfanta\Pagerfanta;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\ArrayAdapter;
use JMS\DiExtraBundle\Annotation as DI;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class PaginationService
 * @package Yarsha\Bundle\MainBundle\Service
 *
 * @DI\Service("yarsha.service.pagination")
 */
class PaginationService
{

    /**
     * @var Pagerfanta
     */
    private $pager;

    /**
     * @var integer
     */
    private $firstIndex = 1;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var integer
     */
    private $limit;

    /**
     * @var Router
     */
    private $router;

    /**
     * PagerfantaService constructor.
     *
     * @DI\InjectParams({
     *      "request" = @DI\Inject("request_stack"),
     *     "router" = @DI\Inject("router")
     * })
     *
     * @param RequestStack $request
     * @param Router $router
     */
    public function __construct(RequestStack $request, Router $router)
    {
        $this->requestStack = $request;
        $this->request = $request->getCurrentRequest();
        $this->router = $router;
    }

    public function getArrayPagerFanta($array)
    {
        $this->request = $this->requestStack->getCurrentRequest();

        $adapter = new ArrayAdapter($array);

        $this->pager = new Pagerfanta($adapter);

        $this->limit = $this->setLimit();

        $this->pager->setMaxPerPage($this->limit);

        if ($this->request->get('page')) {
            $this->pager->setCurrentPage($this->request->get('page'));
        }

        return $this->pager;
    }

    public function getPagerFanta(QueryBuilder $builder)
    {
        $this->request = $this->requestStack->getCurrentRequest();

        $this->limit = $this->setLimit();

        $adapter = new DoctrineORMAdapter($builder, false);

        $this->pager = new Pagerfanta($adapter);

        $this->pager->setMaxPerPage($this->limit);

        if ($this->request->get('page')) {
            $this->pager->setCurrentPage($this->request->get('page'));
        }

        $this->firstIndex = (($this->pager->getCurrentPage() - 1) * $this->pager->getMaxPerPage()) + 1;

        return $this->pager;
    }

    public function getFirstIndex()
    {
        return $this->firstIndex;
    }

    private function setLimit()
    {
        $this->request = $this->requestStack->getCurrentRequest();

        $route = $this->request->get("_route");

        if ($this->request->get('limit') and is_numeric($this->request->get('limit'))) {
            return $this->request->get('limit');
        }

        if (
            "yarsha_admin" == substr($route, 0, strlen('yarsha_admin'))
            or "yarsha_employer" == substr($route, 0, strlen('yarsha_employer'))
        ) {
            return 50;
        }

        return 9;
    }

    public function getRoute($route = '', $field = '', $extraParams = [])
    {
        if ($route == "" or $route == '#') {
            return '#';
        }

        $this->request = $this->requestStack->getCurrentRequest();

        $params = $this->request->query->all();

        unset($params['page']);
        unset($params['limit']);

        if ($field != '') {
            $params['sort'] = $field;
            $params['order'] = (isset($params['order']))
                ? ($params['order'] == 'desc') ? 'asc' : 'desc'
                : 'asc';
        }

        $params = array_merge($params, $extraParams);

        $router = $this->router->generate($route, $params);

        return $router;
    }


}
