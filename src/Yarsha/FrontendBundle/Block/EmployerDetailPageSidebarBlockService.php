<?php

namespace Yarsha\FrontendBundle\Block;

use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\EmployerBundle\Service\EmployerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\OrganizationBundle\Entity\Organization;

/**
 * Class EmployerDetailPageSidebarBlockService
 * @package Yarsha\FrontendBundle\Block
 *
 * @DI\Service("yarsha.block.employer_sidebar")
 * @DI\Tag(name="sonata.block")
 */
class EmployerDetailPageSidebarBlockService extends AbstractBlockService
{


    private $em;

    private $employerService;

    /**
     * EmployerDetailPageSidebarBlockService constructor.
     * @param EngineInterface $templating
     * @param EntityManager $em
     * @param EmployerService $employerService
     * @DI\InjectParams({
     *     "templating" = @DI\Inject("templating"),
     *     "em" = @DI\Inject("doctrine.orm.entity_manager"),
     *     "employerService" = @DI\Inject("yarsha.service.employer")
     * })
     */

    public function __construct(EngineInterface $templating, EntityManager $em, EmployerService $employerService)
    {
        parent::__construct('yarsha.block.employer_sidebar', $templating);

        $this->em = $em;
        $this->employerService = $employerService;
    }

    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'title' => 'Sidebar',
            'template' => 'YarshaFrontendBundle:Block:employer_frontend_sidebar.html.twig',
            'organization' => null,
            'limit' => 5,
            'job' => null
        ]);
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();

        $data['block'] = $blockContext->getBlock();
        $data['settings'] = $settings;
        $data['followers'] = [];
        $data['companies'] = [];
        $data['jobs'] = [];
        $data['topEmployers'] = [];

        $organization = $settings['organization'];
        $orgRepo = $this->em->getRepository(Organization::class);
        if ($organization) {
//            $data['followers'] = $organization->getFollowers();
            $data['followers'] = $orgRepo->getRandomFollowers($organization);
            $data['jobs'] = $this->employerService
                ->getEntityManager()
                ->getRepository(Job::class)
                ->getJobsByOrganization($organization->getId(), 10, $settings['job']);

            $data['companies'] = $this->employerService
                ->getRelatedCompanyJobs($organization->getCategory(), $organization, 10);

            $data['topEmployers'] = $this->employerService->getTopEmployers(4);
        }

        return $this->renderResponse($blockContext->getTemplate(), $data, $response);
    }

}
