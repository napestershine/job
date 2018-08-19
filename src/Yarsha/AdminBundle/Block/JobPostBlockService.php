<?php
/**
 * Created by PhpStorm.
 * User: yarsha
 * Date: 2/15/17
 * Time: 2:27 PM
 */

namespace Yarsha\AdminBundle\Block;

use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\JobsBundle\JobConstants;

/**
 * Class JobPostBlockService
 * @package Yarsha\AdminBundle\Block
 *
 * @DI\Service("yarsha.block.job_post")
 * @DI\Tag(name="sonata.block")
 */
class JobPostBlockService extends AbstractBlockService
{

    private $em;

    /**
     * SuperEmployersBlockService constructor.
     * @param EngineInterface $templating
     * @param EntityManager $em
     *
     * @DI\InjectParams({
     *     "templating" = @DI\Inject("templating"),
     *     "em" = @DI\Inject("doctrine.orm.entity_manager")
     * })
     */

    public function __construct(EngineInterface $templating, EntityManager $em)
    {
        parent::__construct('yarsha.block.job_post', $templating);

        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'url' => false,
            'title' => 'Total Jobs',
            'template' => 'YarshaAdminBundle:Blocks:jobpost.html.twig',
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $jobRepo = $this->em->getRepository("YarshaJobsBundle:Job");
        $jobCounts = $jobRepo->getJobCountByStatus();

        return $this->renderResponse($blockContext->getTemplate(), [
            'count' => $jobCounts[0],
            'setting' => $blockContext->getSettings()
        ], $response);
    }
}

