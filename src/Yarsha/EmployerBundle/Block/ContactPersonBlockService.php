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
use Yarsha\OrganizationBundle\Entity\OrganizationContactPerson;
use Yarsha\AdminBundle\Entity\User;

/**
 * Class ContactPersonBlockService
 * @package Yarsha\EmployerBundle\Block
 * @DI\Service("yarsha.block.employer_contact_person")
 * @DI\Tag(name="sonata.block")
 */
class ContactPersonBlockService extends AbstractAdminBlockService
{

    private $em;

    private $employer;


    /**
     * ContactPersonBlockService constructor.
     * @param EntityManager $entityManager
     * * @DI\InjectParams({
     *     "templating" = @DI\Inject("templating"),
     *  "entityManager" = @DI\Inject("doctrine.orm.entity_manager"),
     *  "tokenStorage" = @DI\Inject("security.token_storage"),
     * })
     */
    public function __construct(
        EngineInterface $templating,
        EntityManager $entityManager,
        TokenStorage $tokenStorage

    ) {
        parent::__construct('yarsha.block.employer_contact_person', $templating);
        $this->em = $entityManager;
        $this->employer = $tokenStorage->getToken()->getUser();

    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'url' => false,
            'title' => 'Profile Summary',
            'template' => 'YarshaEmployerBundle:Blocks:contact_person.html.twig',
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {

        $orgId = $this->employer->getOrganization();
        $organization = $this->em->getRepository(Organization::class)->find($orgId);
        if ($organization) {
            $contactPerson = $this->em->getRepository(User::class)->findOneBy(
                [
                    'id' => $organization->getAccountManager(),
                    'enabled' => 1,
                    'deleted' => 0
                ]
            );
        }
        return $this->renderResponse($blockContext->getTemplate(), [
            'setting' => $blockContext->getSettings(),
            'contactPerson' => $contactPerson
        ], $response);
    }
}

