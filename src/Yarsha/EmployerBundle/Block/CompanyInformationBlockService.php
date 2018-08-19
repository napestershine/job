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
use Yarsha\JobsBundle\Entity\Job;

/**
 * Class CompanyInformationBlockService
 * @package Yarsha\EmployerBundle\Block
 * @DI\Service("yarsha.block.employer_company_information")
 * @DI\Tag(name="sonata.block")
 */
class CompanyInformationBlockService extends AbstractAdminBlockService
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * CompanyInformationBlockService constructor.
     * @param EngineInterface $templating
     * @param EntityManager $em
     *
     *  @DI\InjectParams({
     *     "templating" = @DI\Inject("templating"),
     *     "em" = @DI\Inject("doctrine.orm.default_entity_manager")
     * })
     */
    public function __construct(
        EngineInterface $templating,
        EntityManager $em

    ) {
        parent::__construct('yarsha.block.employer_company_information', $templating);
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'url' => false,
            'title' => 'Company Information',
            'template' => 'YarshaEmployerBundle:Blocks:company_information.html.twig',
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

        $jobs = [];

        if($organization)
        {
            $filters['organization'] = $organization->getId();
            $jobs = $this->em->getRepository(Job::class)->listJobsByEmployer($filters);
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'setting' => $blockContext->getSettings(),
            'jobs' => $jobs
        ], $response);
    }
}

