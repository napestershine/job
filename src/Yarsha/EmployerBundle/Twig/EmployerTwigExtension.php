<?php

namespace Yarsha\EmployerBundle\Twig;


use Assetic\Asset\StringAsset;
use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpKernel\Kernel;
use Yarsha\EmployerBundle\Entity\User as Employer;
use Yarsha\EmployerBundle\Service\EmployerService;
use Yarsha\JobsBundle\Entity\JobLevel;
use Yarsha\JobsBundle\Form\JobType;
use Yarsha\JobsBundle\JobConstants;
use Yarsha\JobSeekerBundle\Entity\User;
use Yarsha\MainBundle\Entity\Category;
use Yarsha\MainBundle\Entity\EducationDegree;
use Yarsha\MainBundle\Entity\Location;
use Yarsha\OrganizationBundle\Entity\Organization;
use Yarsha\JobSeekerBundle\Entity\User as Seeker;
use Yarsha\MainBundle\MainBundleConstants;
use Yarsha\MainBundle\Service\LippImagineService;


/**
 * Class EmployerTwigExtension
 * @package Yarsha\EmployerBundle\Twig
 *
 * @DI\Service("yarsha.twig.employer", public=false)
 * @DI\Tag(name="twig.extension")
 */
class EmployerTwigExtension extends \Twig_Extension
{

    /**
     * @var EmployerService
     */
    private $employerService;


    /**
     * @var Packages
     */
    private $assetsHelper;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var
     */
    private $lippImagineService;

    /**
     * @var string
     */
    private $imageDir;

    /**
     * EmployerTwigExtension constructor.
     * @param EmployerService $employerService
     * @param Packages $assetsHelper
     * @param Router $router
     * @param string $rootDir
     *
     *
     * @DI\InjectParams({
     * "employerService" = @DI\Inject("yarsha.service.employer"),
     * "assetsHelper" = @DI\Inject("assets.packages"),
     * "router" = @DI\Inject("router"),
     * "lippImagineService" = @DI\Inject("yarsha.service.lipp_imagine"),
     * "rootDir" = @DI\Inject("%kernel.root_dir%")
     * })
     */
    public function __construct(

        EmployerService $employerService,
        Packages $assetsHelper,
        Router $router,
        LippImagineService $lippImagineService,
        $rootDir

    ) {
        $this->employerService = $employerService;
        $this->assetsHelper = $assetsHelper;
        $this->router = $router;
        $this->lippImagineService = $lippImagineService;
        $this->imageDir = $rootDir . '/../web';
    }

    public function getName()
    {
        return 'yarsha_twig_employer';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'render_employer_job_status',
                [$this, 'renderEmployerJobStatus'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'render_employer_follower',
                [$this, 'renderEmployerFollower'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'render_employer_follower_image',
                [$this, 'renderEmployerFollowerImage'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'job_status',
                [$this, 'jobStatus'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'gender_type',
                [$this, 'genderType'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'marital_status',
                [$this, 'maritalStatus'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'experience_year',
                [$this, 'experienceYear'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'render_org_banners_carousel',
                [$this, 'renderBannersCarousel'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'render_super_employer_banner',
                [$this, 'renderSuperEmployerBanner']
            ),

        ];
    }

    public function renderEmployerJobStatus($status)
    {
        switch ($status) {
            case JobConstants::JOB_STATUS_PENDING:
                $label = 'Pending';
                $labelClass = 'label-info';
                break;
            case JobConstants::JOB_STATUS_APPROVED:
                $label = 'Approved';
                $labelClass = 'label-success';
                break;
            case JobConstants::JOB_STATUS_DELETED:
                $label = 'Deleted';
                $labelClass = 'label-danger';
                break;
            case JobConstants::JOB_STATUS_DISABLED:
                $label = 'Disabled';
                $labelClass = 'label-warning';
                break;
            default:
                $label = 'Unknown';
                $labelClass = 'label-default';
                break;
        }

        return "<span class=\"label {$labelClass}\">{$label}</span>";

    }

    public function renderEmployerFollower(User $jobSeeker)
    {
//        if ($jobSeeker->getPath()) {
//            $img = $this->assetsHelper->getUrl('uploads/seekers/' . $jobSeeker->getPath());
//        } else {
//            $img = $this->assetsHelper->getUrl('images/20170226125105.png');
//        }

        if ($jobSeeker->getPath() and file_exists($this->imageDir . "/uploads/seekers/" . $jobSeeker->getPath())) {
            $img = $this->lippImagineService->getCachedImageUrl('/uploads/seekers/' . $jobSeeker->getPath(),
                'thumb_medium');
        } else {
            $img = $this->lippImagineService->getCachedImageUrl('images/avatar_default.png',
                'thumb_medium');
        }


        $cvPath = $this->assetsHelper->getUrl('/uploads/getUploadDir/' . $jobSeeker->getCurriculumVitaePath());
        $link = $this->router->generate('yarsha_employer_follower_details', ['username' => $jobSeeker->getUsername()]);
        $title = $jobSeeker->getFullName();
//        $output = "<div class=\"col-md-2 col-sm-2 follower-tab-blk\">";
//        $output .= "<a href=\"$link\" title=\"$title\"><img src=\"$img\"></a>";

//        $output .= "<h1>{$jobSeeker->getfirstName()} {$jobSeeker->getlastName()}</h1>";
//        $output .= "<span>{$jobSeeker->getNoOfYear()} Yrs {$jobSeeker->getNoOfMonth()} Months Experience </span><br>";
//        $output .= "<a target=\"_blank\"  href=\"$cvPath\">Preview CV </a>";
//        $output .= '</div>';


        $output = "<div class=\"col-md-6\">
                        <div class=\"search-detail-blk\">
                            <div class=\"row\">
                                <div class=\"col-md-3\">
                                    <a href=\"$link\"
                                      title=\"$title\">
                                      <img src=\"$img\"></a></div>

                                <div class=\"col-md-9\">
                                    <ul class=\"search-detail-cat-lead\"><li><a href=\"$link\">$title</a></li><li>Experience: ";


        if ($jobSeeker->isHasExperience()) {

            $output .= $jobSeeker->getNoOfYear() ? $jobSeeker->getNoOfYear() . 'Y' : '';
            $output .= '&nbsp;';
            $output .= $jobSeeker->getNoOfMonth() ? $jobSeeker->getNoOfMonth() . 'M' : '';

        } else {
            $output .= 'None';
        }
        $degree = $jobSeeker->getDegree();
        $output .= "<li title=\"$degree\">Education: " . substr(strip_tags($jobSeeker->getDegree()),
                0,
                30) . "</li>";
        $categories = $jobSeeker->getPreferredCategories();

        if (count($categories) > 0) {
            $cate = $categories[0]->getTitle();
            $output .= "<li title=\"$cate\">Category: " . substr(strip_tags($categories[0]->getTitle()),
                    0, 35) . "</li>";
        } else {
            $output .= '<li>Category: None </li>';
        }
        $output .= "</ul>
                                </div>
                            </div>
                        </div>
                    </div>";


        return $output;

    }

    public
    function renderEmployerFollowerImage(
        $follower
    ) {
        if ($follower->getPath() and file_exists($this->imageDir . "/uploads/seekers/" . $follower->getPath())) {
            $img = $this->lippImagineService->getCachedImageUrl('/uploads/seekers/' . $follower->getPath(),
                'thumb_small');
        } else {
            $img = $this->lippImagineService->getCachedImageUrl('images/avatar_default.png',
                'thumb_small');
        }


        $output = '<li class="col-md-4 col-sm-4 job-detail-follower-blk">
                                <img src="' . $img . '" title="' . $follower->getFullName() . '">
                   </li>';

        return $output;
    }

    public
    function jobStatus(
        $selected = null
    ) {


        $options = '<option value="">-- Select Status --</option>';

        $statusType = JobConstants::$jobStatusDesc;

        foreach ($statusType as $k => $v) {
            $selectedAttr = $selected == $k ? 'selected="selected"' : '';
            $options .= "<option value=\"{$k}\" {$selectedAttr}>{$v}</option>";
        }

        $html = '<select name="status" class="form-control">';

        $html .= $options;
        $html .= "</select>";

        return $html;

    }


    public
    function genderType(
        $selected = null,
        $class = null
    ) {


        $options = '<option value="">Select Gender</option>';

        $statusType = MainBundleConstants::$genderDesc;

        foreach ($statusType as $k => $v) {
            $selectedAttr = $selected == $k ? 'selected="selected"' : '';
            $options .= "<option value=\"{$k}\" {$selectedAttr}>{$v}</option>";
        }
        $targetClass = $class == null ? 'form-control' : $class;
        $html = '<select name="gender" class="' . $targetClass . '">';

        $html .= $options;
        $html .= "</select>";

        return $html;

    }

    public
    function maritalStatus(
        $selected = null,
        $class = null
    ) {


        $options = '<option value="">Select Marital Status</option>';

        $statusType = MainBundleConstants::$maritalStatus;

        foreach ($statusType as $k => $v) {
            $selectedAttr = $selected == $k ? 'selected="selected"' : '';
            $options .= "<option value=\"{$k}\" {$selectedAttr}>{$v}</option>";
        }

        $targetClass = $class == null ? 'form-control' : $class;

        $html = '<select name="marital_status" class="' . $targetClass . '">';

        $html .= $options;
        $html .= "</select>";

        return $html;

    }


    public
    function experienceYear(
        $selected = null,
        $class = null
    ) {


        $options = '<option value="">Select Experience Year</option>';

        $statusType = MainBundleConstants::$experienceYear;

        foreach ($statusType as $k => $v) {
            $selectedAttr = $selected == $k ? 'selected="selected"' : '';
            $options .= "<option value=\"{$k}\" {$selectedAttr}>{$v}</option>";
        }

        $targetClass = $class == null ? 'form-control' : $class;

        $html = '<select name="year" class="' . $targetClass . '">';

        $html .= $options;
        $html .= "</select>";

        return $html;

    }

    public
    function renderContactInformation()
    {


    }

    public
    function renderBannersCarousel(
        $organizationId
    ) {
        $banners = $this->employerService->getActiveBanner($organizationId);
        $html = '';

        if (count($banners)) {
            $html = "<div class=\"carousel-inner\">";
            $count = 0;
            foreach ($banners as $banner) {
                $isActiveClass = $count == 0 ? 'active' : '';
                $bannerPath = $this->assetsHelper->getUrl('uploads/employers/' . $banner->getPath());
                $html .= "<div class=\"item $isActiveClass\" >";
                $html .= "<img src=\"$bannerPath\">";
                $html .= "</div>";
                $count++;
            }
            $html .= "</div>";
        } else {
            $defaultBanner = $this->assetsHelper->getUrl('bundles/yarshafrontend/images/bg.jpg');
            $html = "<img src=\"$defaultBanner\" alt=\"\">";
        }

        return $html;
    }

    public
    function renderSuperEmployerBanner(
        $organizationId
    ) {
        $banners = $this->employerService->getActiveBanner($organizationId);

        if (count($banners) > 0) {

            $bannerPath = $this->assetsHelper->getUrl('uploads/employers/' . $banners[0]->getPath());

        } else {
            $bannerPath = $this->assetsHelper->getUrl('bundles/yarshafrontend/images/bg.jpg');

        }

        return $bannerPath;
    }


    public
    function getContactPersonByEmployer(
        $employer
    ) {
        return $this->employerService->getEmployerByOrganization($employer);
    }

    public
    function getOrganizationByEmployer()
    {

    }

}
