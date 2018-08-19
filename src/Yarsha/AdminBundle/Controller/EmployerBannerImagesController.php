<?php

namespace Yarsha\AdminBundle\Controller;

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
 * @package Yarsha\AdminBundle\Controller
 * @Route("/admin")
 *
 */
class EmployerBannerImagesController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/banners", name="yarsha_admin_employer_banner")
     * @Breadcrumb("list")
     */
    public function indexAction(Request $request)
    {

        $data = [];
        $employerId = $this->getUser()->getOrganization()->getId();
        if ($employerId) {

            $em = $this->get('doctrine.orm.entity_manager');
            $banners = $em->getRepository(OrganizationBannerImages::class)->findBy(

                ['order' => 'ASC']

            );
        }

        $data['banners'] = $banners;

        return $this->render('YarshaAdminBundle:Banners:index.html.twig', $data);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/banners/add", name="yarsha_admin_employer_banner_add")
     * @Route("/banners/{id}/update", name="yarsha_admin_employer_banner_edit")
     */
    public function addAction(Request $request)
    {

        $em = $this->get('doctrine.orm.entity_manager');
        $id = $request->get('id');
        $data['updating'] = false;

        if ($id) {
            $banner = $em->getRepository(OrganizationBannerImages::class)->find($id);
            $data['updating'] = true;
        } else {
            $banner = new OrganizationBannerImages();
        }

        $form = $this->createForm(bannerType::class, $banner);
        $breadcrumb = $this->get('apy_breadcrumb_trail');

        if ($organizationId = $request->get('ref')) {
            $organization = $this->get('yarsha.service.organization')
                ->getOrganizationById($organizationId);
//            if ($organization) {
//                $form->get('organization')->setData($organization);
//            }
            $breadcrumb->add($organization->getName(), 'yarsha_admin_organization_detail', ['id' => $organizationId]);
            $breadcrumb->add("Banners", 'yarsha_admin_organization_banners', ['id' => $organizationId]);
        } else {
            $breadcrumb->add("Banners", 'yarsha_admin_employer_banner');
        }
        $breadcrumb->add($data['updating'] ? 'Update' : 'New');

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            try {

                $banner = $form->getData();

                if ($data['updating'] == false) {
                    $banner->setEmployer($organization);
                }

                $banner->upload();
                $resize = new ImageService();
                if ($resize->resizeImage($banner->getAbsolutePath(), $banner->getUploadResizeRootDir(), 1268, 390)) {
                    $em->persist($banner);
                    $em->flush();

                    $successMessage = $data['updating'] ? 'Banner Updated Successfully.' : 'Banner Added Successfully.';
                    $this->addFlash('success', $successMessage);

                    return $this->redirectToRoute('yarsha_admin_organization_banners',
                        ['id' => $organization->getId()]);

                } else {
                    $this->addFlash('errorMessage', 'Please Upload Image size minimum 1268X390');
                }

            } catch (\Exception $e) {

                $this->addFlash('errorMessage', $e->getMessage());
            }
        }
        $data['form'] = $form->createView();

        return $this->render('YarshaAdminBundle:Banners:create.html.twig', $data);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/banner/{id}/delete", name="yarsha_admin_employer_banner_delete")
     */

    public function deleteAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $bannerId = $request->get('id');
        $bannerRepo = $em->getRepository(OrganizationBannerImages::class);
        $banner = $bannerRepo->find($bannerId);
        $response = [];
        if ($banner) {
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
     * @Route("/banner/{id}/feature", name="yarsha_admin_employer_banner_images_feature")
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
