<?php

namespace Yarsha\MainBundle\Twig;

use JMS\DiExtraBundle\Annotation as DI;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Asset\Packages;
use Yarsha\OrganizationBundle\Entity\Organization;
use Yarsha\JobSeekerBundle\Entity\User as Seeker;

/**
 * Class ImageExtension
 * @package Yarsha\MainBundle\Twig
 *
 * @DI\Service("yarsha.service.image_twig_extension", public=false)
 * @DI\Tag(name="twig.extension")
 */
class ImageExtension extends \Twig_Extension
{

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var string
     */
    private $imageDir;

    /**
     * @var Packages
     */
    private $assetsHelper;

    /**
     * ImageExtension constructor.
     *
     * @DI\InjectParams({
     *      "cacheManager" = @DI\Inject("liip_imagine.cache.manager"),
     *      "rootDir" = @DI\Inject("%kernel.root_dir%"),
     *     "assetsHelper" = @DI\Inject("assets.packages")
     * })
     *
     * @param CacheManager $cacheManager
     * @param string $rootDir
     * @param Packages $assetsHelper
     */
    public function __construct(CacheManager $cacheManager, $rootDir, Packages $assetsHelper)
    {
        $this->cacheManager = $cacheManager;
        $this->imageDir = $rootDir . '/../web';
        $this->assetsHelper = $assetsHelper;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'ys_load_image',
                [$this, 'renderImage'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction('organization_profile_image', [$this, 'getOrganizationProfileImage']),
            new \Twig_SimpleFunction('seeker_profile_image', [$this, 'getSeekerProfileImage']),
        ];
    }

    public function renderImage($imagePath = '', $filter = '', $extra = '')
    {

        $imagePath = ($imagePath != '' and file_exists($this->imageDir . urldecode($imagePath)))
            ? $imagePath
            : 'bundles/yarshafrontend/images/img_default.jpg';


        $img = $this->cacheManager->getBrowserPath($imagePath, $filter);

        return "<img src=\"{$img}\" class=\"img-responsive\" {$extra} >";
    }

    public function getOrganizationProfileImage(Organization $organization, $absolute = false)
    {
        $path = $organization->getPath();

        $image = ($path and file_exists($this->imageDir . '/uploads/employers/' . $path))
            ? 'uploads/employers/' . $path
            : 'images/company_logo_default.jpg';

        return $this->assetsHelper->getUrl($image);

    }

    public function getSeekerProfileImage(Seeker $seeker, $default = '')
    {
        $path = $seeker->getPath();

        $image = ($path and file_exists($this->imageDir . '/uploads/seekers/' . $path))
            ? 'uploads/seekers/' . $path
            : 'images/avatar_default.png';

        return $this->assetsHelper->getUrl($image);

    }


    public function getName()
    {
        return 'image_twig_extension';
    }


}
