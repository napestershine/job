<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{

    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\AopBundle\JMSAopBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Rollerworks\Bundle\MultiUserBundle\RollerworksMultiUserBundle(),
            new APY\BreadcrumbTrailBundle\APYBreadcrumbTrailBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new FM\ElfinderBundle\FMElfinderBundle(),

            new Yarsha\AdminBundle\YarshaAdminBundle(),
            new Yarsha\FrontendBundle\YarshaFrontendBundle(),
            new Yarsha\EmployerBundle\YarshaEmployerBundle(),
            new Yarsha\JobSeekerBundle\YarshaJobSeekerBundle(),
            new Yarsha\JobsBundle\YarshaJobsBundle(),
            new Yarsha\OrganizationBundle\YarshaOrganizationBundle(),
            new Yarsha\MainBundle\YarshaMainBundle(),
            new Yarsha\ArticleBundle\YarshaArticleBundle(),
            new Yarsha\TagsBundle\YarshaTagsBundle(),
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
            new Yarsha\AgencyBundle\YarshaAgencyBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle()
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__) . '/var/cache/' . $this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__) . '/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir() . '/config/config_' . $this->getEnvironment() . '.yml');
    }
}
