<?php
/**
 * Created by PhpStorm.
 * User: yarsha-mandip
 * Date: 4/7/17
 * Time: 1:16 PM
 */

namespace Yarsha\JobSeekerBundle\Block;

use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Yarsha\JobsBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Yarsha\JobSeekerBundle\Service\JobSeekerService;

/**
 * Class BrowseJobsBlockService
 * @package Yarsha\JobSeekerBundle\Block
 * @DI\Service("yarsha.block.seeker_applied_jobs")
 * @DI\Tag(name="sonata.block")
 */
class AppliedJobsBlockService extends AbstractAdminBlockService
{

    private $em;

    private $seeker;

    private $seekerService;

    /**
     * BrowseJobsBlockService constructor.
     * @param EntityManager $entityManager
     * * @DI\InjectParams({
     *     "templating" = @DI\Inject("templating"),
     *  "entityManager" = @DI\Inject("doctrine.orm.entity_manager"),
     *  "tokenStorage" = @DI\Inject("security.token_storage"),
     *     "seekerService" = @DI\Inject("yarsha.service.job_seeker")
     * })
     */
    public function __construct(
        EngineInterface $templating,
        EntityManager $entityManager,
        TokenStorage $tokenStorage,
        JobSeekerService $seekerService
    ) {
        parent::__construct('yarsha.block.seeker_applied_jobs', $templating);
        $this->em = $entityManager;
        $this->seeker = $tokenStorage->getToken()->getUser();
        $this->seekerService = $seekerService;
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'url' => false,
            'title' => 'Jobs I Have Applied',
            'template' => 'YarshaJobSeekerBundle:Blocks:appliedjobs.html.twig',
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $appliedJobs = $this->seekerService->getJobsAppliedBySeekerLatest($this->seeker, 5);

        return $this->renderResponse($blockContext->getTemplate(), [
            'setting' => $blockContext->getSettings(),
            'appliedjobs' => $appliedJobs
        ], $response);
    }

}
