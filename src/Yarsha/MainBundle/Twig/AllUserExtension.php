<?php

namespace Yarsha\MainBundle\Twig;

use JMS\DiExtraBundle\Annotation as DI;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

/**
 * Class AllUserExtension
 * @package Yarsha\MainBundle\Twig
 *
 * @DI\Service("yarsha.twig.all_user", public=false)
 * @DI\Tag(name="twig.extension")
 */
class AllUserExtension extends \Twig_Extension
{

    /**
     * @var string
     */
    private $imageDir;


    /**
     * AllUserExtension constructor.
     *
     * @DI\InjectParams({
     *      "rootDir" = @DI\Inject("%kernel.root_dir%")
     * })
     *
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        $this->imageDir = $rootDir . '/../web';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('profile_pic_path', [$this, 'profilePicPath']),
            new \Twig_SimpleFunction('user_detail', [$this, 'userDetail']),
            new \Twig_SimpleFunction('contact_email_json', [$this, 'contactEmailJson'])
        ];
    }

    public function profilePicPath($user)
    {
        $image = "bundles/yarshaadmin/images/user.png";

        if (
            $user instanceof \Yarsha\AdminBundle\Entity\User
            and $user->getPhoto()
            and file_exists($this->imageDir . "/uploads/users/" . $user->getPhoto())
        ) {
            $image = "uploads/users/" . $user->getPhoto();
        } elseif (
            $user instanceof \Yarsha\EmployerBundle\Entity\User
            and $user->getOrganization()
            and ($imageName = $user->getOrganization()->getPath())
            and file_exists($this->imageDir . "/uploads/employers/" . $imageName)
        ) {
            $image = "uploads/employers/" . $imageName;
        } elseif (
            $user instanceof \Yarsha\JobSeekerBundle\Entity\User
            and $user->getPath()
//            and file_exists($this->imageDir . "/uploads/seekers/" . $user->getPath()
//            )
        ) {
            // $image = "uploads/seekers/".$user->getPath();

            if (strpos($user->getPath(), 'https://') !== 0) {
                $image = "/uploads/seekers/" . $user->getPath();
            } else {
                $image = $user->getPath();
            }
        }

        return $image;
    }

    public function contactEmailJson($user)
    {
        $contactEmail = "";
        if ($user instanceof \Yarsha\AdminBundle\Entity\User) {
            $contactEmail = $user->getContactEmail();
        } elseif ($user instanceof \Yarsha\EmployerBundle\Entity\User) {
            $organization = $user->getOrganization();
            if ($organization) {
                $contactPersons = $organization->getContactPersons();
                $contactEmail = is_array($contactPersons) ? $contactPersons[0]->getEmail() : '';
            }
        } elseif ($user instanceof \Yarsha\JobSeekerBundle\Entity\User) {
            $contactEmail = $user->getContactEmail();
        }
        echo json_encode(['contactEmail' => $contactEmail]);
    }

    public function userDetail($user)
    {
        $name = 'Anonymous';
        $dashboardLink = '';
        $profileLink = '';
        $logoutLink = '';

        if ($user instanceof \Yarsha\AdminBundle\Entity\User) {
            $name = $user->getName() ?: $user->getUsername();
            $dashboardLink = 'yarsha_admin_dashboard';
            $profileLink = '';
            $logoutLink = 'yarsha_admin_security_logout';
        } elseif ($user instanceof \Yarsha\EmployerBundle\Entity\User) {
            $name = $user->getOrganization() ? $user->getOrganization()->getName() : $user->getUsername();
            $dashboardLink = 'yarsha_employer_homepage';
            $profileLink = 'yarsha_employer_profile_view';
            $logoutLink = 'yarsha_employer_security_logout';
        } elseif ($user instanceof \Yarsha\JobSeekerBundle\Entity\User) {
            $name = $user->getFullName() ?: ($user->getContactEmail()) ?: $user->getUsername();
            $dashboardLink = 'yarsha_job_seeker_homepage';
            $profileLink = 'yarsha_job_seeker_profile_detail_view';
            $logoutLink = 'yarsha_job_seeker_security_logout';
        }

        return [
            'name' => $name,
            'dashboardLink' => $dashboardLink,
            'profileLink' => $profileLink,
            'logoutLink' => $logoutLink,
        ];
    }

    public function getName()
    {
        return 'all_user_twig_extension';
    }


}
