<?php

namespace Yarsha\AdminBundle\Twig;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Rollerworks\Bundle\MultiUserBundle\Tests\Functional\Bundle\AdminBundle\Entity\Admin;
use Yarsha\AdminBundle\Entity\Advertisement;
use Yarsha\AdminBundle\Entity\User as AdminUser;
use Yarsha\AdminBundle\Service\AdvertisementService;
use Yarsha\AgencyBundle\Entity\User;
use Yarsha\MainBundle\Service\LippImagineService;
use Yarsha\OrganizationBundle\Entity\Organization;
use Yarsha\OrganizationBundle\OrganizationConstants;
use Yarsha\OrganizationBundle\Service\OrganizationService;

/**
 * Class AdvertisementAdminExtension
 * @package Yarsha\AdminBundle\Twig
 *
 * @DI\Service("yarsha.twig.advertisement_admin")
 * @DI\Tag(name="twig.extension")
 */
class AdvertisementAdminExtension extends \Twig_Extension
{

    /**
     * @var AdvertisementService
     */
    private $advertisementService;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var LippImagineService
     */
    private $imagineService;


    /**
     * OrganizationAdminExtension constructor.
     *
     * @DI\InjectParams({
     *     "advertisementService"=@DI\Inject("yarsha.service.advertisement"),
     *      "em"=@DI\Inject("doctrine.orm.entity_manager"),
     *      "imagineService"=@DI\Inject("yarsha.service.lipp_imagine")
     * })
     */
    public function __construct(
        AdvertisementService $advertisementService,
        EntityManager $em,
        LippImagineService $imagineService
    ) {
        $this->advertisementService = $advertisementService;
        $this->em = $em;
        $this->imagineService = $imagineService;
    }

    public function getName()
    {
        return 'yarsha_twig_advertisement_admin';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('render_ad_section_select', [$this, 'renderAdSectionSelect'],
                ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_ad_status_select', [$this, 'renderAdStatusSelect'],
                ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('article_category_type', [$this, 'articleCategoryType'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_agency', [$this, 'renderAgency'], ['is_safe' => ['html']])
        ];
    }

    public function renderAdSectionSelect($selected = '')
    {
        $options = "<option value=\"\">-- All --</option>";

        foreach (Advertisement::$advSections as $k => $v) {
            $selectedAttr = $selected == $k ? 'selected="selected"' : '';
            $options .= "<option value=\"$k\" {$selectedAttr} >{$v['label']}</option>";
        }

        $html = "<select name=\"section\" class=\"form-control\">";
        $html .= $options;
        $html .= "</select>";

        return $html;
    }

    public function renderAdStatusSelect($selected = '')
    {
        $options = "<option value=\"\">-- All --</option>";

        $mappings = ["Y" => "Active", "N" => "Inactive"];

        foreach ($mappings as $k => $v) {
            $selectedAttr = $selected === $k ? 'selected="selected"' : '';
            $options .= "<option value=\"$k\" {$selectedAttr} >{$v}</option>";
        }

        $html = "<select name=\"status\" class=\"form-control\">";
        $html .= $options;
        $html .= "</select>";

        return $html;
    }

    public function articleCategoryType($code)
    {
        switch ($code) {
            case 1:
                $status = "CMS Page";
                break;
            case 2:
                $status = "Employer Services";
                break;
            case 3:
                $status = "Employee Services";
                break;
            case 4:
                $status = "HR Issues";
                break;
            case 5:
                $status = "Career Resources";
                break;
            case 6:
                $status = "Training";
                break;
            case 7:
                $status = "Corporate Services";
                break;
            case 8:
                $status = "Page Block";
                break;
            case 9:
                $status = "Loksewa";
                break;
            default:
                $status = "N/A";
        }

        return $status;

    }

    public function renderAgency($id = '', $class = 'form-control')
    {
        $agencies = $this->em->getRepository(User::class)->findBy(
            [
                'deleted' => false,
                'enabled'=>true
            ]
        );
        $output = "<select id='agency' name='agency'  class='{$class}' >";
        $output .= "<option value=''>-- Select Agency --</option>";
        foreach ($agencies as $agency) {
            $selectedAttr = $id == $agency->getId() ? 'selected="selected"' : '';
            $output .= "<option value='" . $agency->getId() . "' {$selectedAttr}>" . $agency->getName() . "</option>";
        }
        $output .= "</select>";

        return $output;
    }


}
