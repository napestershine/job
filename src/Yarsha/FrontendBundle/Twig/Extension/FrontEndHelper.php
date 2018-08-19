<?php

namespace Yarsha\FrontendBundle\Twig\Extension;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Yarsha\AdminBundle\Entity\Advertisement;
use Yarsha\EmployerBundle\Entity\User as Employer;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\JobsBundle\Entity\JobLevel;
use Yarsha\JobsBundle\JobConstants;
use Yarsha\MainBundle\Entity\Category;
use Yarsha\MainBundle\Entity\EducationDegree;
use Yarsha\MainBundle\Entity\Location;
use Yarsha\MainBundle\Service\LippImagineService;
use Yarsha\OrganizationBundle\Entity\Organization;
use Symfony\Component\Asset\Packages;
use Yarsha\JobSeekerBundle\Entity\User as JobSeeker;
use Yarsha\JobSeekerBundle\Entity\EmployeeAppliedJob;

/**
 * Class OrganizationAdminExtension
 * @package Yarsha\AdminBundle\Twig
 *
 * @DI\Service("yarsha.twig.frontend_helper")
 * @DI\Tag(name="twig.extension")
 */
class FrontEndHelper extends \Twig_Extension
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

    private $lippImagineService;


    /**
     * FrontEndHelper constructor.
     * @param EntityManager $em
     * @param Router $router
     *
     * @DI\InjectParams({
     *      "em"=@DI\Inject("doctrine.orm.entity_manager"),
     *      "router" = @DI\Inject("router"),
     *     "assetsHelper" = @DI\Inject("assets.packages"),
     *     "lippImagineService" = @DI\Inject("yarsha.service.lipp_imagine")
     * })
     */
    public function __construct(
        EntityManager $em,
        Router $router,
        Packages $assetsHelper,
        LippImagineService $lippImagineService
    ) {
        $this->em = $em;
        $this->router = $router;
        $this->assetsHelper = $assetsHelper;
        $this->lippImagineService = $lippImagineService;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('display_job_locations', [$this, 'displayJobLocations'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('display_categories', [$this, 'displayCategories'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('display_job_levels', [$this, 'displayJobLevels'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('display_job_description_types', [$this, 'displayJobDescriptionTypes'],
                ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('display_job_availability_types', [$this, 'displayJobAvailabilityTypes'],
                ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('display_education_degrees', [$this, 'displayEducationDegrees'],
                ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('trim_content', [$this, 'trimContent'],
                ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('set_total_content', [$this, 'setTotalContent']),
            new \Twig_SimpleFunction('check_followed_employer', [$this, 'checkFollowedEmployer']),
            new \Twig_SimpleFunction('count_jobs_by_organization', [$this, 'countJobsByOrganization']),
            new \Twig_SimpleFunction('date_difference', [$this, 'dateDiff']),
            new \Twig_SimpleFunction('government_or_newspaper_job', [$this, 'governmentOrNewsPaperJob']),
            new \Twig_SimpleFunction('display_categories_by_section', [$this, 'displayCategoriesBySection'],
                ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('count_government_jobs', [$this, 'countGovernmentJobs']),
            new \Twig_SimpleFunction('get_hot_jobs_by_organization', [$this, 'getHotJobsByOrganization']),
            new \Twig_SimpleFunction('get_newspaper_jobs_by_organization', [$this, 'getNewspaperJobsByOrganization']),
            new \Twig_SimpleFunction('get_featured_jobs_by_organization', [$this, 'getFeaturedJobByOrganization']),
            new \Twig_SimpleFunction('count_job_vacancies_by_category', [$this, 'countJobVacanciesByCategory']),
            new \Twig_SimpleFunction('count_job_vacancies_by_industry', [$this, 'countJobVacanciesByIndustry']),
            new \Twig_SimpleFunction('render_login_alert_button', [$this, 'renderLoginAlertButton'],
                ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_ads', [$this, 'renderAds'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_follow_button', [$this, 'renderFollowButton'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('get_employer_by_organization', [$this, 'getEmployerByOrganization'],
                ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('check_path', [$this, 'checkPath']),
            new \Twig_SimpleFunction('check_website_path', [$this, 'checkWebsitePath'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_organization_name', [$this, 'renderOrganizationName'],
                ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_category_name', [$this, 'renderCategoryName'],
                ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_job_level', [$this, 'renderJobLevel'],
                ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_degree_name', [$this, 'renderDegreeName'],
                ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_location_name', [$this, 'renderLocationName'],
                ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('check_job_applied', [$this, 'checkJobApplied'],
                ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_job_type_icon', [$this, 'renderJobTypeIcon'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('set_total_link_content', [$this, 'setTotalLinkContent'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('count_followers', [$this, 'countFollowers'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('filterProfile', [$this, 'filter_Profile'], ['is_safe' => ['html']])

        ];
    }


    public function displayJobLocations($id = '', $class = 'form-control', $name = 'location', $attr = '')
    {
        $locations = $this->em->getRepository(Location::class)->findBy(
            ['deleted' => 0],
            ['name' => 'ASC']
        );
        $output = "<select id='{$name}' name='{$name}' class='{$class}' {$attr}>";
        $output .= "<option value=''>Job Locations</option>";
        foreach ($locations as $l) {
            $selectedAttr = $id == $l->getId() ? 'selected="selected"' : '';
            $output .= "<option value='" . $l->getId() . "' {$selectedAttr}>" . $l->getName() . "</option>";
        }
        $output .= "</select>";

        return $output;
    }

    public function displayCategories($id = '', $class = 'form-control', $name = 'category', $label = '', $attr = '')
    {
        $categories = $this->em->getRepository(Category::class)->findBy(['deleted' => 0]);
        $output = "<select id='{$name}' name='{$name}' {$attr} class='{$class}'>";
        $label = $label == '' ? 'Job Categories' : $label;
        $output .= "<option value=''>" . $label . "</option>";
        foreach ($categories as $c) {
            $selectedAttr = $id == $c->getId() ? 'selected="selected"' : '';
            $output .= "<option value='" . $c->getId() . "' {$selectedAttr}>" . $c->getTitle() . "</option>";
        }
        $output .= "</select>";

        return $output;
    }

    public function displayCategoriesBySection($id = '', $class = 'form-control', $name, $type, $title, $attr = '')
    {
        $categories = $this->em->getRepository(Category::class)->findBy(
            [
                'section' => $type,
                'deleted' => 0
            ]
        );
        $output = "<select id='{$name}' name='{$name}' {$attr} class='{$class}' >";
        $output .= "<option value=''>$title</option>";
        foreach ($categories as $c) {
            $selectedAttr = $id == $c->getId() ? 'selected="selected"' : '';
            $output .= "<option value='" . $c->getId() . "' {$selectedAttr}>" . $c->getTitle() . "</option>";
        }
        $output .= "</select>";

        return $output;
    }

    public function displayJobLevels($id = '', $class = 'form-control', $name = 'level', $attr = '')
    {
        $levels = $this->em->getRepository(JobLevel::class)->findBy(['deleted' => 0]);
        $output = "<select name='{$name}' id='{$name}' class='{$class}' {$attr}>";
        $output .= "<option value=''>Job levels</option>";
        foreach ($levels as $level) {
            $selectedAttr = $id == $level->getId() ? "selected = 'selected'" : '';
            $output .= "<option value='{$level->getId()}' {$selectedAttr}>{$level->getName()}</option>";
        }
        $output .= "</select>";

        return $output;
    }

    public function displayJobDescriptionTypes($select = '', $class = 'form-control', $name = 'type', $attr = '')
    {
        $output = "<select name='{$name}' id='{$name}' class='$class' $attr>";
        $output .= "<option value=''>Job Description</option>";
        $types = JobConstants::$jobsTypeDesc;
        foreach ($types as $key => $value) {
            $selectedAttr = $select == $key ? "selected = 'selected'" : '';
            $output .= "<option value='{$key}' $selectedAttr>{$value}</option>";
        }
        $output .= "</select>";

        return $output;
    }

    public function displayJobAvailabilityTypes(
        $select = '',
        $class = 'form-control',
        $name = 'availability',
        $attr = ''
    ) {
        $output = "<select name='{$name}' id='{$name}' class='{$class}' $attr>";
        $output .= "<option value=''>Job availability</option>";
        $types = JobConstants::$jobsAvailabilityDesc;
        foreach ($types as $key => $value) {
            $selectedAttr = $select == $key ? "selected = 'selected'" : '';
            $output .= "<option value='{$key}' $selectedAttr>{$value}</option>";
        }
        $output .= "</select>";

        return $output;
    }

    public function displayEducationDegrees($id = '', $class = 'form-control', $name = 'education', $attr = '')
    {
        $output = "<select name='{$name}' id='{$name}' class='{$class}' {$attr}>";
        $output .= "<option value=''>Education Degrees</option>";
        $degrees = $this->em->getRepository(EducationDegree::class)->findBy(['deleted' => 0]);
        foreach ($degrees as $degree) {
            $selectedAttr = $id == $degree->getId() ? "selected = 'selected'" : '';
            $output .= "<option value='{$degree->getId()}' $selectedAttr>{$degree->getName()}</option>";
        }
        $output .= "</select>";

        return $output;
    }

    public function displaySelect($id, $label, $name, $datas)
    {
        $output = "<label for='level'>{$label}</label><select name='{$name}' id='{$name}' class='form-control'>";
        $output .= "<option value=''>---</option>";
        foreach ($datas as $data) {
            $selectedAttr = $id == $data->getId() ? "selected = 'selected'" : '';
            $output .= "<option value='{$data->getId()}' {$selectedAttr}>{$data->getName()}</option>";
        }
        $output .= "</select>";

        return $output;
    }

    public function setTotalContent($content, $length = 40)
    {
        $length = is_numeric($length) ? $length : 40;

        return $content = substr(strip_tags($content), 0, $length);
    }

    public function trimContent($content, $length = 100)
    {

        return $body = mb_substr(strip_tags($content), 0, $length);

    }

    public function checkFollowedEmployer($seeker, $employer)
    {
        $qb = $this->em->createQueryBuilder();

        $count = $qb->select('e')
            ->from(Employer::class, 'e')
            ->join('e.followers', 'f')
            ->where('f.id = :seekerId')->setParameter('seekerId', $seeker->getId())
            ->andWhere('e.id = :employerId')->setParameter('employerId', $employer->getId())
            ->getQuery()->getResult();

        $result = !empty($count) ? true : false;

        return $result;
    }

    public function getHotJobsByOrganization($organization, $limit = null)
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('j')
            ->from(Job::class, 'j')
            ->join(Organization::class, 'o')
            ->where('j.jobsFrom = :jobsFrom')->setParameter('jobsFrom', JobConstants::JOB_FROM_EMPLOYERS)
            ->andWhere('j.status = :status')->setParameter('status', JobConstants::JOB_STATUS_APPROVED)
            ->andWhere('j.type = :type')->setParameter('type', JobConstants::JOBS_TYPE_HOT)
            ->andWhere('j.deadline >= :today')->setParameter('today', date('Y-m-d'))
            ->andWhere('j.organization = :orgId')->setParameter('orgId', $organization->getId())
            ->addOrderBy('j.id', 'DESC');

        if (is_numeric($limit) && $limit > 0) {
            $qb->getMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function getNewspaperJobsByOrganization($organization, $limit = null)
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('j')
            ->from(Job::class, 'j')
            ->join(Organization::class, 'o')
            ->where('j.jobsFrom = :jobsFrom')->setParameter('jobsFrom', JobConstants::JOB_FROM_NEWSPAPER)
            ->andWhere('j.status = :status')->setParameter('status', JobConstants::JOB_STATUS_APPROVED)
            ->andWhere('j.deadline >= :today')->setParameter('today', date('Y-m-d'))
            ->andWhere('j.organization = :orgId')->setParameter('orgId', $organization->getId())
            ->addOrderBy('j.id', 'DESC');

        if (is_numeric($limit) && $limit > 0) {
            $qb->getMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function getFeaturedJobByOrganization($organization, $limit = null)
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('j')
            ->from(Job::class, 'j')
            ->join(Organization::class, 'o')
            ->andWhere('j.status = :status')->setParameter('status', JobConstants::JOB_STATUS_APPROVED)
            ->andWhere('j.type = :type')->setParameter('type', JobConstants::JOBS_TYPE_FEATURED)
            ->andWhere('j.deadline >= :today')->setParameter('today', date('Y-m-d'))
            ->andWhere('j.organization = :orgId')->setParameter('orgId', $organization->getId())
            ->addOrderBy('j.id', 'DESC');

        if (is_numeric($limit) && $limit > 0) {
            $qb->getMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function countJobsByOrganization($organization)
    {
        $qb = $this->em->createQueryBuilder();
        $results = $qb->select('j')
            ->from(Job::class, 'j')
            ->where('j.organization = :orgId')->setParameter('orgId', $organization->getId())
            ->andWhere('j.deadline >= :today')->setParameter('today', date('Y-m-d'))
            ->andWhere('j.status = :status')->setParameter('status', JobConstants::JOB_STATUS_APPROVED)
            ->getQuery()->getResult();

        return count($results);
    }

    public function dateDiff($date)
    {
        $currentDate = new \DateTime();
        $date1 = date_create($date);
        $date2 = date_create($currentDate->format('Y-m-d'));

        $diff = date_diff($date2, $date1);

        $day = $diff->format("%R%a");
        if ($day == 0) {
            return '(Today)';
        } elseif ($day > 0) {
            return $diff->format("(%a days left)");
        } else {
            return '(Expired)';
        }
    }

    public function governmentOrNewsPaperJob($job)
    {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select('j')
            ->from(Job::class, 'j')
            ->where('j.id = :jobId')
            ->andWhere('j.jobsFrom = :government')
            ->orWhere('j.jobsFrom = :newspaper')
            ->andWhere('j.deadline >= :today')->setParameter('today', date('Y-m-d'))
            ->setParameter('jobId', $job->getId())
            ->setParameter('government', JobConstants::JOB_FROM_GOVERNMENT)
            ->setParameter('newspaper', JobConstants::JOB_FROM_NEWSPAPER)
            ->getQuery()->getResult();
        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function countGovernmentJobs()
    {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select('j')
            ->from(Job::class, 'j')
            ->andWhere('j.jobsFrom = :government')->setParameter('government', JobConstants::JOB_FROM_GOVERNMENT)
            ->andWhere('j.status = :status')->setParameter('status', JobConstants::JOB_STATUS_APPROVED)
            ->andWhere('j.deadline >= :today')->setParameter('today', date('Y-m-d'))
            ->getQuery()->getResult();

        return count($result);
    }

    public function countJobVacanciesByCategory($category)
    {
        $vacancyCount = 0;
        $qb = $this->em->createQueryBuilder();
        $jobs = $qb->select('j')
            ->from(Job::class, 'j')
            ->where('j.category = :category')->setParameter('category', $category->getId())
            ->andWhere('j.status = :status')->setParameter('status', JobConstants::JOB_STATUS_APPROVED)
            ->andWhere('j.deadline >= :today')->setParameter('today', date('Y-m-d'))
            ->getQuery()->getResult();
        foreach ($jobs as $job) {
            if (is_numeric($job->getNumberOfVacancies()) && $job->getNumberOfVacancies() > 0) {
                $vacancies = $job->getNumberOfVacancies();
            } else {
                $vacancies = 0;
            }

            $vacancyCount = $vacancyCount + $vacancies;
        }

        return $vacancyCount;

    }

    public function countJobVacanciesByIndustry($industry)
    {
        $vacancyCount = 0;
        $qb = $this->em->createQueryBuilder();
        $jobs = $qb->select('j')
            ->from(Job::class, 'j')
            ->where('j.industry = :industry')->setParameter('industry', $industry->getId())
            ->andWhere('j.status = :status')->setParameter('status', JobConstants::JOB_STATUS_APPROVED)
            ->andWhere('j.deadline >= :today')->setParameter('today', date('Y-m-d'))
            ->getQuery()->getResult();
        foreach ($jobs as $job) {
            if (is_numeric($job->getNumberOfVacancies()) && $job->getNumberOfVacancies() > 0) {
                $vacancies = $job->getNumberOfVacancies();
            } else {
                $vacancies = 0;
            }

            $vacancyCount = $vacancyCount + $vacancies;
        }

        return $vacancyCount;

    }

    public function renderLoginAlertButton1($class, $iconClass = '', $label)
    {
        $output = "<a class=\"login-alert-btn {$class}\" href=\"#\" data-toggle=\"modal\" data-target=\"#login-alert-modal\">";
        if ($iconClass != "") {
            $output .= "<i class=\"fa {$iconClass}\" aria-hidden=\"true\"></i> &nbsp;";
        }
        $output .= $label;
        $output .= "</a>";

        return $output;
    }

    public function renderLoginAlertButton($type, $class = '')
    {
        $mapping = $this->loginAlertMappings($type);

        if ($mapping['userType'] == 'provider') {
//            $loginRoute = $this->router->generate('yarsha_job_seeker_security_login', ['t' => 'p']);
            $loginRoute = $this->router->generate('yarsha_employer_security_login');
            $registerRoute = $this->router->generate('yarsha_frontend_register_employer');
            $forgotRoute = $this->router->generate('yarsha_employer_resetting-request');
        } else {
            $registerRoute = $this->router->generate('yarsha_frontend_register_as_seeker');
            $loginRoute = $this->router->generate('yarsha_job_seeker_security_login');
            $forgotRoute = $this->router->generate('yarsha_job_seeker_resetting-request');
        }

        $loginButton = "<a href=\"$loginRoute\">Login</a>";
        $registerButton = "<a href=\"$registerRoute\">Sign up</a>";

//        $mapping['message'] = str_replace(['sign up', 'login'], [$registerButton, $loginButton], $mapping['message']);

        $output = "<a class=\"login-alert-btn {$class}\" href=\"#\" data-toggle=\"modal\" data-target=\"#login-alert-modal\" data-type=\"$type\" data-message=\"{$mapping['message']}\" data-forgot=\"{$forgotRoute}\" data-login=\"{$loginRoute}\" data-register=\"{$registerRoute}\">";

        if ($mapping['iconClass'] != "") {
            $output .= "<i class=\"{$mapping['iconClass']}\" aria-hidden=\"true\"></i> &nbsp;";
        }

        $output .= $mapping['label'];
        $output .= "</a>";

        return $output;
    }

    public function renderFollowButton(Organization $organization, $user = null, $extraClass = '')
    {
        if ($user instanceof JobSeeker) {
            $isFollowing = $organization->isFollowedBy($user);
            $orgId = $organization->getId();
            $faClass = 'fa-plus';
            $buttonLabel = 'Follow Us';

            if ($isFollowing) {
                $faClass = 'fa-minus-circle';
                $buttonLabel = 'Unfollow';
            }

            $output = "<a href=\"#\" data-employer-id=\"$orgId\" class=\"toggle-follow $extraClass\" data-status=\"$isFollowing\">";
            $output .= "<i class=\"fa $faClass\" aria-hidden=\"true\"></i> &nbsp;$buttonLabel </a>";
            $output .= "</a>";

        } else {
            $output = $this->renderLoginAlertButton('follow', $extraClass);
        }

        return $output;
    }

    public function loginAlertMappings($type)
    {
        $mappings = [
            'follow' => [
                'label' => 'Follow Us',
                'iconClass' => 'fa fa-plus',
                'message' => 'Please sign up or login as job seeker (if you already have an account) to follow.',
                'userType' => 'seeker'
            ],
            'apply' => [
                'label' => 'Apply Now',
                'iconClass' => 'fa fa-hand-pointer-o',
                'message' => 'You need to sign up or login as job seeker (if you already have an account) to apply for the post.',
                'userType' => 'seeker'
            ],
            'apply_email' => [
                'label' => 'Apply Email',
                'iconClass' => 'fa fa-envelope-o',
                'message' => 'You need to sign up or login as job seeker (if you already have an account) to apply for the post.',
                'userType' => 'seeker'
            ],
            'post_job' => [
                'label' => 'Post Job',
                'iconClass' => '',
                'message' => 'You need to sign up or login (if you already have an account) as an employer before posting a job.',
                'userType' => 'provider'
            ],
            'post_resume' => [
                'label' => 'Post Resume',
                'iconClass' => '',
                'message' => 'You need to sign up or login as job seeker (if you already have an account) before posting your resume.',
                'userType' => 'provider'
            ],
            'add_to_basket' => [
                'label' => 'Add To Basket',
                'iconClass' => 'fa fa-shopping-basket',
                'message' => 'You need to sign up or login as job seeker (if you already have an account) before adding job to basket.',
                'userType' => 'seeker'
            ],
            'search_resume' => [
                'label' => 'Search Resume',
                'iconClass' => '',
                'message' => 'You need to sign up or login (if you already have an account) as an employer before searching job seeker resume.',
                'userType' => 'provider'
            ],
            'share_job' => [
                'label' => 'Share Job',
                'iconClass' => 'fa fa-share-square-o',
                'message' => 'You need to sign up or login as job seeker (if you already have an account) to share a job.',
                'userType' => 'seeker'
            ],
            'default' => [
                'label' => 'Login',
                'iconClass' => '',
                'message' => 'Please login to continue.',
                'userType' => 'seeker'
            ],
            'cv_view_more' => [
                'label' => 'See More',
                'iconClass' => '',
                'message' => 'Please sign up or login as employer (if you already have an account) to request CV.',
                'userType' => 'provider'
            ],
            'request_cv' => [
                'label' => 'REQUEST CV',
                'iconClass' => '',
                'message' => 'Please sign up or login as employer (if you already have an account) to request CV.',
                'userType' => 'provider'
            ]
            ];

        return isset($mappings[$type]) ? $mappings[$type] : $mappings['default'];
    }

    public function renderAds($type = null, $limit)
    {
        $output = '';

        if ($type == 'hot') {
            $ads = $this->em->getRepository(Advertisement::class)->getAdvertisementsForFrontend(Advertisement::ADV_POSITION_HOME_PAGE_HOT_JOB_SECTION,
                $limit);
            foreach ($ads as $ad) {
                $imgSrc = $this->lippImagineService->getCachedImageUrl('/uploads/advertisements/' . $ad->getPath(),
                    'hot_job_ad');
                $output .= '<div class="col-md-4 col-sm-6 hot-job-row-blk ">';
                $output .= '<div class="hot-job-sec-blk hot-job-ind-wrap hot-job-ads clearfix">';
                $output .= '<img src="' . $imgSrc . '">';
                // $output .= '<img src="'.$this->assetsHelper->getUrl('/uploads/advertisements/'.$ad->getPath()).'">';
                $output .= '</div></div>';
            }
        } elseif ($type == 'newspaper') {
            $ads = $this->em->getRepository(Advertisement::class)->getAdvertisementsForFrontend(Advertisement::ADV_POSITION_HOME_PAGE_NEWSPAPER_JOB_SECTION,
                $limit);
            foreach ($ads as $ad) {
                $imgSrc = $this->lippImagineService->getCachedImageUrl('/uploads/advertisements/' . $ad->getPath(),
                    'newspaper_job_ad');
                $output .= '<div class="col-md-3 col-sm-6 newspaper_job_ad">';
                $output .= '<img src="' . $imgSrc . '">';
                $output .= '</div>';
            }
        } elseif ($type == 'recent') {
            $ads = $this->em->getRepository(Advertisement::class)->getAdvertisementsForFrontend(Advertisement::ADV_POSITION_HOME_PAGE_RECENT_JOB_SECTION,
                $limit);
            foreach ($ads as $ad) {
                $imgSrc = $this->lippImagineService->getCachedImageUrl('/uploads/advertisements/' . $ad->getPath(),
                    'recent_job_ad');
                $output .= '<li class="col-md-4 col-sm-6 recent-ads">'; // recent-jobs-btm-3
                $output .= '<div class="row">';
                $output .= '<img src="' . $imgSrc . '">';
                $output .= '</div>';
                $output .= '</li>';
            }
        }

        return $output;
    }

    public function getEmployerByOrganization(Organization $organization)
    {
        $employer = $this->em->getRepository(Employer::class)->findOneBy([
            'organization' => $organization
        ]);

        return $employer;
    }

    public function checkPath($var)
    {
        if (strpos($var, 'https://') !== 0) {
            return $this->assetsHelper->getUrl('/uploads/seekers/' . $var);
        } else {
            return $var;
        }
    }


    public function checkWebsitePath($website)
    {

        $output = '';

        if ($website != '') {
            if (strpos($website, 'http://') !== 0) {
                $output .= ' <a target="_blank" class="a"
                                   href="http://' . $website . '">
                                   <i class="fa fa-external-link-square"></i> &nbsp;
                            Visit Website
                         </a>';
            } else {
                $output .= ' <a target="_blank" class="b"
                                   href="' . $website . '">
                                   <i class="fa fa-external-link-square"></i> &nbsp;
                            Visit Website
                         </a>';
            }
        }

        return $output;

    }

    public function renderOrganizationName($organizationId)
    {

        $employer = $this->em->getRepository(Organization::class)->find($organizationId);


        return '<a class="btn btn-sm btn-success">' . $employer . '</a>';
    }


    public function renderCategoryName($catId)
    {

        $category = $this->em->getRepository(Category::class)->find($catId);


        return '<a class="btn btn-sm btn-info">' . $category . '</a>';
    }


    public function renderJobLevel($levelId)
    {

        $level = $this->em->getRepository(JobLevel::class)->find($levelId);


        return '<a class="btn btn-sm btn-danger">' . $level . '</a>';
    }

    public function renderDegreeName($degreeId)
    {

        $degree = $this->em->getRepository(EducationDegree::class)->find($degreeId);


        return '<a class="btn btn-sm btn-success">' . $degree . '</a>';
    }


    public function renderLocationName($locationId)
    {

        $location = $this->em->getRepository(Location::class)->find($locationId);


        return '<a class="btn btn-sm btn-warning">' . $location . '</a>';
    }

    public function checkJobApplied($job, $user)
    {
        $output = '';

        $appliedJob = $this->em->getRepository(EmployeeAppliedJob::class)->findOneBy([
                'employee' => $user,
                'job' => $job
            ]
        );

        if ($appliedJob) {

            $output .= "<a><i class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i> &nbsp;Applied</a>";


        } else {
//            $output = "<a href=\"javascript:void(0)\" onclick=\"onlinejobapply('{$job->getSlug()}')\">
//                                        <i class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i> &nbsp;
//                                        Apply
//                                        </a>";
            $path = $this->router->generate(
                'yarsha_frontend_job_detail_view', ['slug' => $job->getSlug()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $output = "<a href=\"{$path}\"><i class=\"fa fa-hand-pointer-o\"></i>&nbsp;Apply Now</a>";
        }

        return $output;
    }

    public function renderJobTypeIcon($job)
    {
        $output = '<i class="fa fa-history" aria-hidden="true"></i>';
        if ($job->getOrganization()) {
            if ($job->getOrganization()->getCategoryType() == 'super') {
                $output = '<i class="fa fa-bookmark" aria-hidden="true"></i>';
            } else {
                if ($job->getType() == 'hot') {
                    $output = '<span class="glyphicon glyphicon-fire"></span>';
                } elseif ($job->getType() == 'newspaper') {
                    $output = '<i class="fa fa-newspaper-o" aria-hidden="true"></i>';
                } elseif ($job->getType() == 'featured') {
                    $output = '<i class="fa fa-check-square-o" aria-hidden="true"></i>';
                } else {
                    $output = '<i class="fa fa-history" aria-hidden="true"></i>';
                }
            }
        } else {
            if ($job->getType() == 'hot') {
                $output = '<span class="glyphicon glyphicon-fire"></span>';
            } elseif ($job->getType() == 'newspaper') {
                $output = '<i class="fa fa-newspaper-o" aria-hidden="true"></i>';
            } elseif ($job->getType() == 'featured') {
                $output = '<i class="fa fa-check-square-o" aria-hidden="true"></i>';
            } else {
                $output = '<i class="fa fa-history" aria-hidden="true"></i>';
            }
        }

        return $output;
    }

    public function setTotalLinkContent($path, $content, $length, $style = '')
    {
        $output = '';
        $title = '';
        if (strlen($content) > $length) {
            $mainContent = substr(strip_tags($content), 0, $length - 2);
            $mainContent .= '..';
            $title = !empty($title) ? $title : $content;
        } else {
            $mainContent = $content;
        }
        $title = addslashes($title);
        $mainContent = addslashes($mainContent);
        $path = addslashes($path);
        $style = addslashes($style);

        $output = "<a href='{$path}' title='{$title}' style='{$style}'>{$mainContent}</a>";


        return $output;
    }

    public function countFollowers($organization)
    {
        $followers = $this->em->getRepository(Organization::class)->getAllFollowers($organization);

        return count($followers);
    }


    public function filter_Profile($content)
    {

        return $filterContent = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $content);

    }

//    public function setHtmlTagWithContent($tagName, $content, $length, $class='', $style=''){
//
//    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'front_end_helper';
    }
}
