<?php

namespace Yarsha\MainBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Asset\Packages;

/**
 * Class LippImagineService
 * @package Yarsha\MainBundle\Service
 * @DI\Service("yarsha.service.lipp_imagine", parent="yarsha.service.abstract")
 * @DI\Tag("yarsha.abstract_service")
 */
class LippImagineService extends AbstractService
{
    /**
     * @var CacheManager
     */
    private $imagineCacheManager;

    /**
     * @var Packages
     */
    private $assetsHelper;


    /**
     * LippImagineService constructor.
     * @param CacheManager $imagineCacheManager
     * @param Packages $assetsHelper
     *
     * @DI\InjectParams({
     *      "imagineCacheManager" = @DI\Inject("liip_imagine.cache.manager"),
     *     "assetsHelper" = @DI\Inject("assets.packages")
     * })
     */
    public function __construct(CacheManager $imagineCacheManager, Packages $assetsHelper)
    {
        $this->imagineCacheManager = $imagineCacheManager;
        $this->assetsHelper = $assetsHelper;
    }

    public function getCachedImageUrl($path, $filter = '')
    {
        $imagePath = $this->assetsHelper->getUrl($path);

        if($filter == '') return $imagePath;

        return $this->imagineCacheManager->getBrowserPath($path, $filter);
    }

}
