<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yarsha\FrontendBundle\Service;

use Yarsha\JobsBundle\Entity\Job;
use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Model\Metadata;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class JobBlockService extends AbstractAdminBlockService
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
            'title' => 'Job List',
            'template' => 'YarshaFrontendBundle:Block:job_list.html.twig',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('settings', 'sonata_type_immutable_array', [
            'keys' => [
                ['url', 'url', ['required' => false]],
                ['title', 'text', ['required' => false]],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        $errorElement
            ->with('settings[url]')
            ->assertNotNull([])
            ->assertNotBlank()
            ->end()
            ->with('settings[title]')
            ->assertNotNull([])
            ->assertNotBlank()
            ->assertLength(['max' => 50])
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // merge settings
        $settings = $blockContext->getSettings();

        $currentDate = new \DateTime();

        $jobs = $this->em->getRepository(Job::class)->getJobs($currentDate->format('Y-m-d'), false);

        return $this->renderResponse($blockContext->getTemplate(), [
            'jobs' => $jobs,
            'block' => $blockContext->getBlock(),
            'settings' => $settings,
        ], $response);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata($this->getName(), (!is_null($code) ? $code : $this->getName()), false, 'SonataBlockBundle',
            [
                'class' => 'fa fa-rss-square',
            ]);
    }
}
