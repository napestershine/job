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
use Yarsha\EmployerBundle\Service\EmployerProfileService;
use Yarsha\OrganizationBundle\Entity\Organization;

/**
 * Class ProfileStatusBlockService
 * @package Yarsha\EmployerBundle\Block
 * @DI\Service("yarsha.block.employer_profile_status")
 * @DI\Tag(name="sonata.block")
 */
class ProfileStatusBlockService extends AbstractAdminBlockService
{

    private $em;

    private $employer;

    private $profileService;

    /**
     * ProfileStatusBlockService constructor.
     * @param EntityManager $entityManager
     * * @DI\InjectParams({
     *     "templating" = @DI\Inject("templating"),
     *  "entityManager" = @DI\Inject("doctrine.orm.entity_manager"),
     *  "tokenStorage" = @DI\Inject("security.token_storage"),
     *     "profileService" = @DI\Inject("yarsha.service.employer_profile")
     * })
     */
    public function __construct(
        EngineInterface $templating,
        EntityManager $entityManager,
        TokenStorage $tokenStorage,
        EmployerProfileService $profileService
    ) {
        parent::__construct('yarsha.block.seeker_profile_status', $templating);
        $this->em = $entityManager;
        $this->employer = $tokenStorage->getToken()->getUser();
        $this->profileService = $profileService;
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'url' => false,
            'title' => 'Profile Summary',
            'template' => 'YarshaEmployerBundle:Blocks:profilestatus.html.twig',
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $profile = $this->em->getRepository(Organization::class)->findOneBy([
            'id' => $this->employer->getOrganization()
        ]);
        if ($profile) {
            $this->profileService->setProfileCompletionStatus($this->employer);
            $status = [
                'OrganizationInformation' => EmployerProfileService::$organizationInformationStatus,
                'ContactPersonInformation' => EmployerProfileService::$contactPersonStatus,
                'Overall' => EmployerProfileService::$overall

            ];
        } else {
            $status = [
                'OrganizationInformation' => 0,
                'ContactPersonInformation' => 0,
                'Overall' => 0
            ];
        }


        return $this->renderResponse($blockContext->getTemplate(), [
            "Status" => $status,
            'setting' => $blockContext->getSettings()
        ], $response);
    }
}

