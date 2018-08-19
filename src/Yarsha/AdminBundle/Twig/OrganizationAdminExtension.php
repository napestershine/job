<?php

namespace Yarsha\AdminBundle\Twig;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Rollerworks\Bundle\MultiUserBundle\Tests\Functional\Bundle\AdminBundle\Entity\Admin;
use Yarsha\AdminBundle\Entity\User as AdminUser;
use Yarsha\MainBundle\Service\LippImagineService;
use Yarsha\OrganizationBundle\Entity\Organization;
use Yarsha\OrganizationBundle\OrganizationConstants;
use Yarsha\OrganizationBundle\Service\OrganizationService;

/**
 * Class OrganizationAdminExtension
 * @package Yarsha\AdminBundle\Twig
 *
 * @DI\Service("yarsha.twig.organization_admin")
 * @DI\Tag(name="twig.extension")
 */
class OrganizationAdminExtension extends \Twig_Extension
{

    /**
     * @var OrganizationService
     */
    private $organizationService;

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
     * @param OrganizationService $organizationService
     *
     * @DI\InjectParams({
     *     "organizationService"=@DI\Inject("yarsha.service.organization"),
     *      "em"=@DI\Inject("doctrine.orm.entity_manager"),
     *      "imagineService"=@DI\Inject("yarsha.service.lipp_imagine")
     * })
     */
    public function __construct(OrganizationService $organizationService, EntityManager $em, LippImagineService $imagineService)
    {
        $this->organizationService = $organizationService;
        $this->em = $em;
        $this->imagineService = $imagineService;
    }

    public function getName()
    {
        return 'yarsha_twig_organization_admin';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('org_next_status', [$this, 'getSelectStatus'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('org_status_buttons', [$this, 'getChangeStatusButtons'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('org_status_label', [$this, 'orgStatusLabel'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('org_category', [$this, 'orgCategory'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_account_manager_select', [$this, 'accountManagerSelect'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_account_manager_detail', [$this, 'renderAccountManager'], ['is_safe' => ['html']])
        ];
    }

    public function getSelectStatus($status)
    {
        $nextStatus = $this->organizationService->statusMappings($status);

        $options = '<option value=""> -- select status --</option>';

        if (count($nextStatus)) {
            foreach ($nextStatus as $s) {
                $label = OrganizationConstants::$organizationStatus[$s];
                $options .= "<option value=\"{$s}\">{$label}</option>";
            }
        }

        return $options;
    }

    public function getChangeStatusButtons($status, $organizationId)
    {
//        $button = "<a class='changeStatus btn btn-xs btn-warning' href='#'
// data-next-status='BTN_NEXT_STATUS' data-org='$organizationId'
// onclick='return changeOrganizationStatus(this)'><i class='fa fa-ban'></i> BTN_LABEL</a>";

        $button = "<li><a class='changeStatus' href='javascript:void(0);'
 data-next-status='BTN_NEXT_STATUS' data-org='$organizationId'
 onclick='return changeOrganizationStatus(this)'> BTN_LABEL</a></li>";

        $nextStatus = $this->organizationService->statusMappings($status);

        $links = '';

        if (count($nextStatus)) {
            foreach ($nextStatus as $s) {
                $label = OrganizationConstants::$organizationStatus[$s];
                $link = str_replace(['BTN_NEXT_STATUS', 'BTN_LABEL'], [$s, $label], $button);
                $links .= $link;
            }
        }

        return $links;
    }

    public function orgStatusLabel($status)
    {
        switch( $status ){
            case OrganizationConstants::ORGANIZATION_STATUS_APPROVED:
                $label = 'Approved';
                $labelClass = 'label-success';
                break;
            case OrganizationConstants::ORGANIZATION_STATUS_PENDING:
                $label = 'Pending';
                $labelClass = 'label-info';
                break;
            case OrganizationConstants::ORGANIZATION_STATUS_DISABLED:
                $label = 'Disabled';
                $labelClass = 'label-warning';
                break;
            default:
                $label = 'Unknown';
                $labelClass = 'label-default';
                break;
        }

        $html = "<span class=\"label {$labelClass}\">{$label}</span>";
//        $html .= "<span class=\"label label-default\" title=\"Change Status\"><i class=\"fa fa-exchange\"></i></span>";

        return $html;
    }

    public function orgCategory($orgId)
    {
        $organization = $this->em->getRepository(Organization::class)->find($orgId);
        $categories = Organization::$orgCategory;
        $option = '<select class="changeCategory form-control" data-org-id="' . $orgId . '">';
        foreach ($categories as $key => $value) {
            $selected = '';
            if ($value == $organization->getCategoryType()) {
                $selected = 'selected';
            }
            $option = $option . '<option ' . $selected . ' value=' . $value . '>' . $key . '</option>';
        }
        $option = $option . '</select>';

        return $option;
    }

    public function accountManagerSelect($organizationId, $selected = "")
    {
        $accountManagers = $this->em->getRepository(AdminUser::class)->getAccountManagersForSelect();

        $html = "<select class=\"changeAccountManager form-control\" data-org-id=\"$organizationId\" data-old-value=\"$selected\">";

        $options = "<option value=\"\"> -- Select Account Manager -- </option>";

        foreach ($accountManagers as $manager) {
            $selectedAttr = ($selected == $manager['id']) ? 'selected="selected"' : '';
            $options .= "<option value=\"{$manager['id']}\" $selectedAttr > {$manager['name']} </option>";
        }
        $html .= $options;
        $html .= '</select>';

        return $html;
    }

    public function renderAccountManager(AdminUser $user = null)
    {
        if($user == null) return '<span class="text-red">No Account Manager Selected</span>';

        $path = ($user->getPhoto())
            ? 'uploads/users/'.$user->getPhoto()
            : 'images/avatar_default.png';

        $photo = $this->imagineService->getCachedImageUrl($path, 'thumb_mini');

        $html = '';
//        $html .= "<img src=\"$photo\">";
        $html .= '<p><i class="fa fa-user"></i>  &nbsp; ' . $user->getName() . '<p>';
        $html .= '<p><i class="fa fa-at"></i>  &nbsp; ' . $user->getContactEmail() . '<p>';


        return $html;
    }

}
