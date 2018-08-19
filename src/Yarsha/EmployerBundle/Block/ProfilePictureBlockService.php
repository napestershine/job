<?php

namespace Yarsha\EmployerBundle\Block;

use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Yarsha\EmployerBundle\Entity\User;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\OrganizationBundle\Entity\OrganizationBannerImages;
use Yarsha\EmployerBundle\Service\EmployerService;

/**
 * Class ProfilePictureBlockService
 * @package Yarsha\EmployerBundle\Block
 * @DI\Service("yarsha.block.employer_profile_picture")
 * @DI\Tag(name="sonata.block")
 */
class ProfilePictureBlockService extends AbstractAdminBlockService
{

    private $em;

    private $employer;

    private $employerService;


    /**
     * ProfilePictureBlockService constructor.
     * @param EntityManager $entityManager
     * * @DI\InjectParams({
     *     "templating" = @DI\Inject("templating"),
     *  "entityManager" = @DI\Inject("doctrine.orm.entity_manager"),
     *  "tokenStorage" = @DI\Inject("security.token_storage"),
     *  "employerService" = @DI\Inject("yarsha.service.employer")
     * })
     */
    public function __construct(
        EngineInterface $templating,
        EntityManager $entityManager,
        TokenStorage $tokenStorage,
        EmployerService $employerService

    ) {
        parent::__construct('yarsha.block.seeker_profile_picture', $templating);
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
            'template' => 'YarshaEmployerBundle:Blocks:profilepicture.html.twig',
            'organization' => null,
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {

        $settings = $blockContext->getSettings();
        $organization = $settings['organization'];

        $jobsCount = 0;
        $total_follower = 0;
        $bannerImage = null;
        $total_applicant = 0;

        if($organization)
        {
            $filters['organization'] = $organization->getId();
            $jobs = $this->em->getRepository(Job::class)->listJobsByEmployer($filters);
            $jobsCount = count($jobs);
            $total_follower = count($organization->getFollowers());

            $bannerImage = $this->em->getRepository(OrganizationBannerImages::class)->findOneBy(
                [
                    'employer' => $organization->getId(),
                    'isFeatured' => true
                ]
            );

            $total_applicant = $this->em->getRepository(User::class)->getTotalApplicantsByOrganization($organization->getId());
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'setting' => $blockContext->getSettings(),
            'banner' => $bannerImage,
            'count' => $total_applicant,
            'jobcount' => $jobsCount,
            'organization' => $organization,
            'follower' => $total_follower
        ], $response);
    }
}

