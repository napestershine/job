<?php
/**
 * Created by PhpStorm.
 * User: zone
 * Date: 1/31/17
 * Time: 5:30 PM
 */

namespace Yarsha\ArticleBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Yarsha\ArticleBundle\Entity\Testimonial;
use Yarsha\ArticleBundle\Form\TestimonialType;
use Yarsha\MainBundle\MainBundleEvents;
use Yarsha\TagsBundle\Event\PostEvent;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Class TestimonialController
 * @package Yarsha\ArticleBundle\Controller
 * @Breadcrumb("Testimonial",routeName="yarsha_admin_testimonial_list")
 */
class TestimonialController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/admin/testimonial/list", name="yarsha_admin_testimonial_list")
     *
     */
    public function indexAction(Request $request)
    {

        $filters = $request->query->all();

        $testimonialService = $this->get('yarsha.service.testimonial');
        $testimonials = $testimonialService->getPaginatedTestimonialList($filters);

        $data['testimonials'] = $testimonials;

        return $this->render('YarshaArticleBundle:Testimonial:list.html.twig', $data);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/admin/testimonial/create", name="yarsha_admin_testimonial_create")
     * @Route("/admin/testimonial/{id}/update", name="yarsha_admin_testimonial_update")
     */
    public function addAction(Request $request)
    {
        $id = $request->get('id');

        $testimonialService = $this->get('yarsha.service.testimonial');

        if ($id) {
            $testimonial = $testimonialService->getTestimonialById($id);

            if (!$testimonial) {
                throw new NotFoundHttpException;
            }

            if (!$testimonialService->hasPermissionToUpdatePost($testimonial)) {
                throw new AccessDeniedException;
            }

            $isUpdating = true;


        } else {
            $testimonial = new Testimonial();
            $isUpdating = false;

        }

        $form = $this->createForm(TestimonialType::class, $testimonial);

        $form->handleRequest($request);
        $this->get('apy_breadcrumb_trail')->add($isUpdating ? $testimonial->getTitle() : 'New');

        if ($form->isSubmitted() and $form->isValid()) {
            $postData = $form->getData();

            try {
                $testimonialService->flushTestimonial($postData, $isUpdating);

                return $this->redirectToRoute('yarsha_admin_testimonial_list');
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }

        $data['form'] = $form->createView();
        $data['updating'] = $isUpdating;

        return $this->render('YarshaArticleBundle:Testimonial:create.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/admin/testimonial/{id}/delete", name="yarsha_admin_testimonial_delete")
     */
    public function deleteAction(Request $request)
    {
        $id = $request->get('id');
        $testimonialService = $this->get('yarsha.service.testimonial');
        $testimonial = $testimonialService->getTestimonialById($id);
        if (!$testimonial) {
            return NotFoundHttpException::class;
        }
        $testimonial->setIsDeleted(true);

        $testimonialService->persist($testimonial);
        $testimonialService->flush();

        return $this->redirectToRoute('yarsha_admin_testimonial_list');

    }

}
