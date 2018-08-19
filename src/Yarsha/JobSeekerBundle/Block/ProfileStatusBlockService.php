<?php
/**
 * Created by PhpStorm.
 * User: yarsha
 * Date: 2/22/17
 * Time: 2:42 PM
 */

namespace Yarsha\JobSeekerBundle\Block;

use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Yarsha\JobSeekerBundle\Entity\ProfileCompletion;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Yarsha\JobSeekerBundle\Service\JobSeekerProfileService;

/**
 * Class ProfileStatusBlockService
 * @package Yarsha\JobSeekerBundle\Block
 * @DI\Service("yarsha.block.seeker_profile_status")
 * @DI\Tag(name="sonata.block")
 */
class ProfileStatusBlockService extends AbstractAdminBlockService
{

    private $em;

    private $seeker;

    private $profileService;

    /**
     * ProfileStatusBlockService constructor.
     * @param EntityManager $entityManager
     * * @DI\InjectParams({
     *  "templating" = @DI\Inject("templating"),
     *  "entityManager" = @DI\Inject("doctrine.orm.entity_manager"),
     *  "tokenStorage" = @DI\Inject("security.token_storage"),
     *  "profileService" = @DI\Inject("yarsha.service.jobseeker_profile")
     * })
     */
    public function __construct(
        EngineInterface $templating,
        EntityManager $entityManager,
        TokenStorage $tokenStorage,
        JobSeekerProfileService $profileService
    ) {
        parent::__construct('yarsha.block.seeker_profile_status', $templating);
        $this->em = $entityManager;
        $this->seeker = $tokenStorage->getToken()->getUser();
        $this->profileService = $profileService;
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'url' => false,
            'title' => 'profile summary',
            'template' => 'YarshaJobSeekerBundle:Blocks:profilestatus.html.twig',
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $status = $this->profileService->getJobSeekerProfileStatus($this->seeker);

        return $this->renderResponse($blockContext->getTemplate(), [
            "Status" => $status,
            'setting' => $blockContext->getSettings()
        ], $response);
    }
}
