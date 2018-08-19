<?php
/**
 * Created by PhpStorm.
 * User: yarsha
 * Date: 2/15/17
 * Time: 3:59 PM
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

/**
 * Class TotalSeekerBlockService
 * @package Yarsha\AdminBundle\Block
 *
 * @DI\Service("yarsha.block.total_seekers")
 * @DI\Tag(name="sonata.block")
 */
class TotalSeekerBlockService extends AbstractBlockService
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
        parent::__construct('yarsha.block.total_seekers', $templating);

        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'url' => false,
            'title' => 'Total Job Seekers',
            'template' => 'YarshaAdminBundle:Blocks:totalseeker.html.twig',
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $seekerRepo = $this->em->getRepository("YarshaJobSeekerBundle:User");
        $seekers_count = $seekerRepo->countSeekersByStatus();

        return $this->renderResponse($blockContext->getTemplate(), [
            'setting' => $blockContext->getSettings(),
            'count' => $seekers_count[0]
        ], $response);
    }
}

