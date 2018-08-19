<?php

namespace Yarsha\EmployerBundle\Controller;

use Yarsha\OrganizationBundle\Entity\OrganizationBannerImages;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Yarsha\OrganizationBundle\Form\bannerType;
use Symfony\Component\HttpFoundation\JsonResponse;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Yarsha\EmployerBundle\Service\ImageService;


/**
 * Class EmployerBannerImagesController
 * @package Yarsha\EmployerBundle\Controller
 * @Route("/employer")
 * @Breadcrumb("Banners", routeName="yarsha_employer_banner_images")
 *
 */
class EmployerBannerImagesController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/banners", name="yarsha_employer_banner_images")
     * @Breadcrumb("list")
     */
    public function indexAction(Request $request)
    {

        $data = [];
        $orgId = $this->getUser()->getOrganization()->getId();
        if ($orgId) {

            $em = $this->get('doctrine.orm.entity_manager');
            $banners = $em->getRepository(OrganizationBannerImages::class)->findBy(

                ['employer' => $orgId],
                ['order' => 'ASC']

            );
        }

        $data['banners'] = $banners;

        return $this->render('YarshaEmployerBundle:Banners:index.html.twig', $data);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/banners/add", name="yarsha_employer_banner_images_add")
     * @Route("/banners/{id}/update", name="yarsha_employer_banner_images_edit")
     */
    public function addAction(Request $request)
    {

        $em = $this->get('doctrine.orm.entity_manager');
        $id = $request->get('id');
        $this->denyAccessUnlessGranted('ROLE_EMPLOYER');
        $user = $this->getUser()->getOrganization();
        $data['updating'] = false;

        if ($id) {
            $banner = $em->getRepository(OrganizationBannerImages::class)->find($id);
            $data['updating'] = true;
            $this->get('apy_breadcrumb_trail')->add('Update');
        } else {
            $banner = new OrganizationBannerImages();
            $this->get('apy_breadcrumb_trail')->add('New');
        }
        $form = $this->createForm(bannerType::class, $banner);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            try {

                $banner = $form->getData();

                if ($data['updating'] == false) {
                    $banner->setEmployer($user);
                }

                $banner->upload();
                $resize = new ImageService();
                if ($resize->resizeImage($banner->getAbsolutePath(), $banner->getUploadResizeRootDir(), 1268, 350)) {
                    $em->persist($banner);
                    $em->flush();

                    $successMessage = $data['updating'] ? 'Banner Updated Successfully.' : 'Banner Added Successfully.';
                    $this->addFlash('success', $successMessage);

                    return $this->redirectToRoute('yarsha_employer_banner_images');

                } else {
                    $this->addFlash('errorMessage', 'Please Upload Image size minimum 1268X350');
                }

            } catch (\Exception $e) {

                $this->addFlash('errorMessage', $e->getMessage());
            }
        }
        $data['form'] = $form->createView();

        return $this->render('YarshaEmployerBundle:Banners:create.html.twig', $data);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/banner/{id}/delete", name="yarsha_employer_banner_images_delete")
     */

    public function deleteAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $bannerId = $request->get('id');
        $bannerRepo = $em->getRepository(OrganizationBannerImages::class);
        $banner = $bannerRepo->find($bannerId);
        $response = [];
        if ($banner) {
            // $banner->deActivate();
            $em->remove($banner);
            try {
                $em->flush();
                $response['status'] = 'success';
            } catch (\Exception $e) {
                $response['status'] = $e->getMessage();
            }
        }

        return new JsonResponse($response);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/banner/{id}/feature", name="yarsha_employer_banner_images_feature")
     */

    public function makefeaturedAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $bannerId = $request->get('id');
        $bannerRepo = $em->getRepository(OrganizationBannerImages::class);
        $banner = $bannerRepo->find($bannerId);
        $response = [];
        if ($banner) {

            if ($banner->getIsFeatured() == 1) {
                $banner->deactivateFeatured();
            } else {
                $others_banner = $em->getRepository(OrganizationBannerImages::class)->findFeaturedBanner($banner->getId(),
                    $banner->getEmployer());
                if ($others_banner) {
                    foreach ($others_banner as $b) {
                        $b->deactivateFeatured();
                    }
                }
                $banner->makeFeatured();
            }


            $em->persist($banner);
            try {
                $em->flush();
                $response['status'] = 'success';
            } catch (\Exception $e) {
                $response['status'] = $e->getMessage();
            }
        }

        return new JsonResponse($response);
    }


}
