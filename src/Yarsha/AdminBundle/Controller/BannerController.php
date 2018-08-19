<?php

namespace Yarsha\AdminBundle\Controller;

use Yarsha\AdminBundle\Entity\Banner;
use Yarsha\AdminBundle\Form\BannerType;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


/**
 * Class BannerController
 * @package Yarsha\AdminBundle\Controller
 *
 * @Route("/admin")
 * @Breadcrumb("banners")
 */
class BannerController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/banners", name="yarsha_admin_banners_list")
     */
    public function indexAction(Request $request)
    {
        $data = [];

        $em = $this->get('doctrine.orm.entity_manager');
        $bannerRepo = $em->getRepository(Banner::class);

        $data['banners'] = $bannerRepo->findBy(['deleted' => 0], ['order' => 'asc', 'id' => 'desc']);

        $form = $this->createForm(BannerType::class, new Banner());

        if ("POST" == $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $banner = $form->getData();
                    if ($banner->getIsFeatured()) {
                        $featuredBanner = $bannerRepo->findOneBy(['isFeatured' => 1]);
                        if ($featuredBanner) {
                            $featuredBanner->setIsFeatured(false);
                            $em->persist($featuredBanner);
                        }
                    }

                    $banner->upload();

                    $em->persist($banner);
                    $em->flush();
                    $this->addFlash('success', 'Banner Added Successfully.');

                    return $this->redirectToRoute('yarsha_admin_banners_list');
                } catch (\Exception $e) {
                    $data['errorMessage'] = $e->getMessage();
                    $data['gotError'] = true;
                }
            } else {
                $data['gotError'] = true;
            }
        }

        $data['form'] = $form->createView();

        return $this->render('@YarshaAdmin/Banner/index.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("banner/{id}/mark-as-featured", name="yarsha_admin_banner_make_featured")
     */
    public function markBannerAsFeaturedAction(Request $request)
    {
        $bannerId = $request->get('id');
        $em = $this->get('doctrine.orm.entity_manager');
        $bannerRepo = $em->getRepository(Banner::class);
        $banner = $bannerRepo->find($bannerId);

        if ($banner) {

            try {
                $featuredBanner = $bannerRepo->findOneBy(['isFeatured' => 1]);
                if ($featuredBanner) {
                    $featuredBanner->setIsFeatured(false);
                    $em->persist($featuredBanner);
                }

                $banner->setIsFeatured(true);
                $em->persist($banner);
                $em->flush();
                $this->addFlash('success', 'Banner marked as featured.');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'No banner selected.');
        }

        return $this->redirectToRoute('yarsha_admin_banners_list');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("banner/{id}/delete", name="yarsha_admin_banner_delete")
     */
    public function deleteBannerAction(Request $request)
    {
        $bannerId = $request->get('id');
        $em = $this->get('doctrine.orm.entity_manager');
        $banner = $em->getRepository(Banner::class)->find($bannerId);

        if ($banner) {

            try {
                $banner->setDeleted(true);
                $em->persist($banner);
                $em->flush();
                $this->addFlash('success', 'Banner deleted successfully.');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'No banner selected.');
        }

        return $this->redirectToRoute('yarsha_admin_banners_list');
    }
}
