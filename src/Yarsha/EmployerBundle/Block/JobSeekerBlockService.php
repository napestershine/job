<?php
/**
 * Created by PhpStorm.
 * User: yarsha
 * Date: 2/22/17
 * Time: 5:21 PM
 */

namespace Yarsha\EmployerBundle\Block;

use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Yarsha\EmployerBundle\Service\EmployerService;


/**
 * Class JobSeekerBlockService
 * @package Yarsha\EmployerBundle\Block
 * @DI\Service("yarsha.block.employer_job_seeker")
 * @DI\Tag(name="sonata.block")
 */
class JobSeekerBlockService extends AbstractAdminBlockService
{

    private $em;

    private $employer;

    private $employerService;


    /**
     * JobSeekerBlockService constructor.
     * @param EntityManager $entityManager
     * * @DI\InjectParams({
     *     "templating" = @DI\Inject("templating"),
     *  "entityManager" = @DI\Inject("doctrine.orm.entity_manager"),
     *  "tokenStorage" = @DI\Inject("security.token_storage"),
     *  "employerService" =@DI\Inject("yarsha.service.employer")
     * })
     */
    public function __construct(
        EngineInterface $templating,
        EntityManager $entityManager,
        TokenStorage $tokenStorage,
        EmployerService $employerService

    ) {
        parent::__construct('yarsha.block.employer_job_seeker', $templating);
        $this->em = $entityManager;
        $this->employer = $tokenStorage->getToken()->getUser();
        $this->employerService = $employerService;

    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'url' => false,
            'title' => 'Profile Summary',
            'template' => 'YarshaEmployerBundle:Blocks:job_seeker.html.twig',
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $employerId = $this->employer;
        $total_applicant = $this->employerService->getPaginatedTotalApplicant($employerId->getId());
        $follower = $employerId->getOrganization();
        $total_follower = $follower->getFollowers();

        return $this->renderResponse($blockContext->getTemplate(), [
            'setting' => $blockContext->getSettings(),
            'applicants' => $total_applicant,
            'followers' => $total_follower
        ], $response);
    }
}

