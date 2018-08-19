<?php

namespace Yarsha\MainBundle\Twig;

use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\AdminBundle\Service\LocationService;

/**
 * Class LocationExtension
 * @package Yarsha\MainBundle\Twig
 *
 * @DI\Service("yarsha.service.location_twig_extension", public=false)
 * @DI\Tag(name="twig.extension")
 */
class LocationExtension extends \Twig_Extension
{
    /**
     * @var LocationService
     */
    private $locationService;


    /**
     * LocationExtension constructor.
     *
     * @DI\InjectParams({
     *      "locationService" = @DI\Inject("yarsha.service.location")
     * })
     *
     * @param LocationService $locationService
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('render_location_select',[$this, 'renderLocationSelect'],['is_safe' => ['html']])
        ];
    }

    public function renderLocationSelect($selected = null)
    {
        $locations = $this->locationService->getLocationsForSelect($filters = []);

        $options = "<option value=\"\">All</option>";

        foreach ($locations as $location) {
            $selectedAttr = $selected == $location['id'] ? 'selected="selected"' : '';
            $options .= "<option value=\"{$location['id']}\" {$selectedAttr}>{$location['name']}</option>";
        }

        $html = "<select name=\"location\" class=\"form-control\">";

        $html .= $options;
        $html .= "</select>";

        return $html;
    }

    public function getName()
    {
        return 'location_twig_extension';
    }


}
