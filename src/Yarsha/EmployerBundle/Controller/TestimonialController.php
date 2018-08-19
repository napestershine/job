<?php
/**
 * Created by PhpStorm.
 * User: zone
 * Date: 1/31/17
 * Time: 5:30 PM
 */

namespace Yarsha\EmployerBundle\Controller;


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
 * @package Yarsha\EmployerBundle\Controller
 * @Breadcrumb("Story",routeName="yarsha_employer_testimonial_list")
 */
class TestimonialController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/employer/testimonial/list", name="yarsha_employer_testimonial_list")
     * @Breadcrumb("List")
     */
    public function indexAction(Request $request)
    {


        $statusType = Testimonial::$testimonialStatusOptions;

        $filters = $request->query->all();
        $testimonialService = $this->get('yarsha.service.testimonial');
        $testimonials = $testimonialService->getPaginatedEmployerTestimonialList($filters);

        $data['testimonials'] = $testimonials;
        $data['statusType'] = $statusType;

        return $this->render('YarshaEmployerBundle:Testimonial:list.html.twig', $data);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/employer/testimonial/create", name="yarsha_employer_testimonial_create")
     * @Route("/employer/testimonial/{id}/update", name="yarsha_employer_testimonial_update")
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

            $this->get('apy_breadcrumb_trail')->add($testimonial->getTitle());


        } else {
            $testimonial = new Testimonial();
            $isUpdating = false;
            $this->get('apy_breadcrumb_trail')->add('New');

        }

        $form = $this->createForm(TestimonialType::class, $testimonial);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $postData = $form->getData();

            try {
                $testimonialService->flushTestimonial($postData, $isUpdating);
                $successMessage = $isUpdating ? 'Story Updated Successfully.' : 'Story Added Successfully.';

                $this->addFlash('success', $successMessage);

                return $this->redirectToRoute('yarsha_employer_testimonial_list');
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }

        $data['form'] = $form->createView();

        return $this->render('YarshaEmployerBundle:Testimonial:create.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/employer/testimonial/{id}/delete", name="yarsha_employer_testimonial_delete")
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

        return $this->redirectToRoute('yarsha_employer_testimonial_list');

    }

}
