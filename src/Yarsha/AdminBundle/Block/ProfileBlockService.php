<?php
/**
 * Created by PhpStorm.
 * User: yarsha-mandip
 * Date: 4/5/17
 * Time: 7:15 PM
 */

namespace Yarsha\AdminBundle\Block;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Templating\EngineInterface;
use Yarsha\JobSeekerBundle\JobSeekerConstants;
use Yarsha\JobSeekerBundle\Service\JobSeekerService;

/**
 * Class ProfileBlockService
 * @package Yarsha\JobSeekerBundle\Block
 * @DI\Service("yarsha.block.admin_seeker_profile")
 * @DI\Tag("sonata.block")
 */
class ProfileBlockService extends AbstractAdminBlockService
{

    private $em;

    private $seekerService;

    private $seeker;

    /**
     * ProfileBlockService constructor.
     * @DI\InjectParams({
     *     "templating" = @DI\Inject("templating"),
     *     "em" = @DI\Inject("doctrine.orm.default_entity_manager"),
     *     "seekerService" = @DI\Inject("yarsha.service.job_seeker"),
     *     "tokenStorage" = @DI\Inject("security.token_storage")
     *     })
     */
    public function __construct(
        EngineInterface $templating,
        EntityManager $em,
        JobSeekerService $seekerService,
        TokenStorage $tokenStorage
    ) {
        parent::__construct("yarsha.block.admin_seeker_profile", $templating);
        $this->em = $em;
        $this->seekerService = $seekerService;
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'title' => null,
            'template' => '@YarshaAdmin/Blocks/education_information.html.twig',
            'section' => 'education',
            'seeker' => null,
            'employer' => false
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $setting = $blockContext->getSettings();
        $data = [];
        $seeker = $setting['seeker'];

        $user = $seeker->getId();

        if (
            JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_PERSONAL == $setting['section']
            or JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_GENERAL == $setting['section']
        ) {
            $data = $seeker;
        } elseif (JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_OTHER == $setting['section']) {
            $data['languages'] = $this->seekerService
                ->getSeekerProfile($seeker, JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_LANGUAGE);

            $data['references'] = $this->seekerService
                ->getSeekerProfile($seeker, JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_REFERENCE);
        } else {
            $data = $this->seekerService->getSeekerProfile($seeker, $setting['section']);
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'setting' => $setting,
            'data' => $data,
            'user' => $user
        ], $response);
    }

}
