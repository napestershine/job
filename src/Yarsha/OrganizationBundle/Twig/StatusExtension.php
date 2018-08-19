<?php

namespace Yarsha\OrganizationBundle\Twig;

use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\OrganizationBundle\Entity\Organization;
use Yarsha\OrganizationBundle\Entity\OrganizationContactPerson;
use Yarsha\OrganizationBundle\OrganizationConstants;
use Yarsha\JobsBundle\JobConstants;
use Yarsha\OrganizationBundle\Service\OrganizationService;
use Yarsha\ArticleBundle\Entity\Testimonial;

/**
 * Class StatusExtension
 * @package Yarsha\OrganizationBundle\Twig
 *
 * @DI\Service("yarsha.twig_status")
 * @DI\Tag(name="twig.extension")
 */
class StatusExtension extends \Twig_Extension
{

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * StatusExtension constructor.
     * @param OrganizationService $organizationService
     *
     * @DI\InjectParams({
     *     "organizationService"= @DI\Inject("yarsha.service.organization")
     * })
     */
    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('change_status', [$this, 'changeStatus']),
            new \Twig_SimpleFunction('change_job_status', [$this, 'changeJobStatus']),
            new \Twig_SimpleFunction('render_job_status', [$this, 'renderJobStatus'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_organization_select', [$this, 'renderOrganizationSelect'],
                ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_organization_category_type_select',
                [$this, 'renderOrganizationCategoryTypeSelect'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_organization_status_select', [$this, 'renderOrganizationStatusSelect'],
                ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_organization_contact_person_info',
                [$this, 'renderOrganizationContactPersonInfo'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('testimonial_status', [$this, 'testimonialStatus'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('jobseeker_status', [$this, 'jobSeekerStatus'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_job_status_select', [$this, 'renderJobStatusSelect'],
                ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_job_type_select', [$this, 'renderJobTypeSelect'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('get_contact_information', [$this, 'getContactInformation']),
            new \Twig_SimpleFunction(
                'render_contact_information',
                [$this, 'renderContactInformation'],
                ['is_safe' => ['html']]
            )
        ];
    }

    public function changeJobStatus($status)
    {
        if ($status == JobConstants::JOB_STATUS_PENDING) {
            return 'Approve';
        } elseif ($status == JobConstants::JOB_STATUS_APPROVED) {
            return 'Disable';
        } else {
            return 'Approve';
        }
    }

    public function changeStatus($status)
    {
        if ($status == OrganizationConstants::ORGANIZATION_STATUS_PENDING) {
            return 'Approve';
        } elseif ($status == OrganizationConstants::ORGANIZATION_STATUS_APPROVED) {
            return 'Disable';
        } else {
            return 'Approve';
        }
    }

    public function renderJobStatus($code)
    {
        switch ($code) {
            case 200:
                $status = "PENDING";
                $class = "label label-primary";
                break;
            case 201:
                $status = "APPROVED";
                $class = "label label-success";
                break;
            case 202:
                $status = "DISABLED";
                $class = "label label-warning";

                break;
            case 203:
                $status = "DELETED";
                $class = "label label-danger";

                break;
            default:
                $status = "";
                $class = "";
        }

        return '<span class="' . $class . '">' . $status . '</span>';

    }

    public function jobSeekerStatus($code)
    {
        switch ($code) {
            case 'pending':
                $status = "APPLICATION SENT";
                $class = "label label-primary";
                break;
            case 'selected':
                $status = "SELECTED";
                $class = "label label-success";
                break;
            case 'save':
                $status = "SAVED";
                $class = "label label-info";

                break;
            case 'shortlisted':
                $status = "SHORTLISTED";
                $class = "label label-warning";

                break;
            case 'rejected':
                $status = "NOT ELIGIBLE";
                $class = "label label-danger";

                break;
            default:
                $status = "";
                $class = "";
        }

        return '<span class="' . $class . '">' . $status . '</span>';

    }

    public function renderOrganizationSelect($selected = '', $class = 'form-control')
    {
        $organizations = $this->organizationService->getOrganizationsForSelect();

        $options = "<option value=\"\">-- select employer --</option>";

        foreach ($organizations as $organization) {
            $selectedAttr = $selected == $organization['id'] ? 'selected="selected"' : '';
            $options .= "<option value=\"{$organization['id']}\" {$selectedAttr}>{$organization['name']}</option>";
        }

        $html = "<select name=\"organization\" class=\"{$class}\">";

        $html .= $options;
        $html .= "</select>";

        return $html;
    }

    public function renderOrganizationCategoryTypeSelect($selected = null)
    {
        $options = "<option value=\"\">-- Select Type--</option>";

        $categoryTypes = Organization::$organizationCategoryTypes;

        foreach ($categoryTypes as $k => $v) {
            $selectedAttr = $selected == $k ? 'selected="selected"' : '';
            $options .= "<option value=\"{$k}\" {$selectedAttr}>{$v}</option>";
        }

        $html = "<select name=\"category\" class=\"form-control\">";

        $html .= $options;
        $html .= "</select>";

        return $html;
    }


    public function testimonialStatus($selected = null)
    {


        $options = '<option value="">-- Select Status --</option>';

        $statusType = Testimonial::$testimonialStatusOptions;

        foreach ($statusType as $k => $v) {
            $selectedAttr = $selected == $k ? 'selected="selected"' : '';
            $options .= "<option value=\"{$k}\" {$selectedAttr}>{$v}</option>";
        }

        $html = '<select name="status" class="form-control">';

        $html .= $options;
        $html .= "</select>";

        return $html;

    }


    public function renderOrganizationStatusSelect($selected = null)
    {
        $options = "<option value=\"\">-- Select Status --</option>";

        $status = OrganizationConstants::$organizationStatus;

        foreach ($status as $k => $v) {
            $selectedAttr = $selected == $k ? 'selected="selected"' : '';
            $options .= "<option value=\"{$k}\" {$selectedAttr}>{$v}</option>";
        }

        $html = "<select name=\"status\" class=\"form-control\">";

        $html .= $options;
        $html .= "</select>";

        return $html;
    }

    public function renderOrganizationContactPersonInfo(Organization $organization)
    {
        $contactPersons = $organization->getContactPersons();

        $contactPerson = null;

        foreach ($contactPersons as $cp) {
            if ($cp->getContactType() == OrganizationContactPerson::CONTACT_TYPE_MAIN_CONTACT) {
                $contactPerson = $cp;
                break;
            };
        }

        $html = '';

        if ($contactPerson) {
            $html .= '<p><i class="fa fa-user"></i>  &nbsp; ' . $contactPerson->getName() . '<p>';
//            $html .= '<p><i class="fa fa-map-marker"></i>  &nbsp; ' . $organization->getAddress() . '<p>';
            $html .= '<p><i class="fa fa-phone"></i>  &nbsp; ' . $contactPerson->getPhone() . '<p>';
            $html .= '<p><i class="fa fa-at"></i>  &nbsp; ' . $contactPerson->getEmail() . '<p>';

        }

        return $html;
    }

    public function renderJobStatusSelect($selected = null)
    {
        $options = "<option value=\"\">-- select Status --</option>";

        $jobStatus = JobConstants::$jobStatusDesc;

        unset($jobStatus[JobConstants::JOB_STATUS_DELETED]);

        foreach ($jobStatus as $k => $v) {
            $selectedAttr = $selected == $k ? 'selected="selected"' : '';
            $options .= "<option value=\"$k\" {$selectedAttr}>{$v}</option>";
        }

        $html = "<select name=\"status\" class=\"form-control\">";

        $html .= $options;
        $html .= "</select>";

        return $html;
    }

    public function renderJobTypeSelect($selected = null)
    {
        $options = "<option value=\"\">-- select type --</option>";

        $jobTypes = JobConstants::$jobsTypeDesc;

        foreach ($jobTypes as $k => $v) {
            $selectedAttr = $selected == $k ? 'selected="selected"' : '';
            $options .= "<option value=\"$k\" {$selectedAttr}>{$v}</option>";
        }

        $html = "<select name=\"type\" class=\"form-control\">";

        $html .= $options;
        $html .= "</select>";

        return $html;
    }

    public function getName()
    {
        return 'twig_change_status';
    }

    public function getContactInformation($org)
    {
        $response['email'] = '';
        $response['mobile'] = '';
        $contactPerson = $this->organizationService->getContactPersonDetailsByOrganizationId($org);
        if ($contactPerson) {
            $response['email'] = $contactPerson->getEmail();
            $response['mobile'] = $contactPerson->getMobile();
        }

        return $response;
    }


    public function renderContactInformation($org)
    {


        $contactPerson = $this->organizationService->getContactPersonDetailsByOrganizationId($org);

        $output = '<li><i class="fa fa-phone-square" aria-hidden="true"></i>&nbsp;' . $contactPerson->getMobile() . '</li><li><i class="fa fa-envelope" aria-hidden="true"></i> &nbsp;' . $contactPerson->getEmail() . '</li>';


        return $output;


    }

}
