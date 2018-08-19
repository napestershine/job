<?php

namespace Yarsha\FrontendBundle\Block;

use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Yarsha\MainBundle\Entity\Category;

/**
 * Class JobCatagoryBlockService
 * @package Yarsha\FrontendBundle\Block
 */
class JobCatagoryBlockService extends AbstractAdminBlockService
{

    public $em;

    public function setEntityManager(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'url' => false,
            'title' => 'Job\'s By Category',
            'template' => 'YarshaFrontendBundle:Blocks:jobcategory.html.twig',
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $jobsByFunction = $this->em->getRepository(Category::class)
            ->findJobsCountByCategorySection(Category::CATEGORY_TYPE_JOB_BY_FUNCTION);

        $jobsByIndustry = $this->em->getRepository(Category::class)
            ->findJobsCountByCategorySection(Category::CATEGORY_TYPE_JOB_BY_INDUSTRY);

        return $this->renderResponse($blockContext->getTemplate(), [
            'jobsByFunction' => $jobsByFunction,
            'jobsByIndustry' => $jobsByIndustry
        ], $response);
    }
}
