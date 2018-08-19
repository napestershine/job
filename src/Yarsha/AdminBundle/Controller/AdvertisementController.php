<?php

namespace Yarsha\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yarsha\AdminBundle\Entity\Advertisement;
use Yarsha\AdminBundle\Form\AdvertisementType;

/**
 * Class AdvertisementController
 * @package Yarsha\AdminBundle\Controller
 * @Route("/admin")
 * @Breadcrumb("Advertisements", routeName="yarsha_admin_advertisements_list")
 */
class AdvertisementController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/advertisements", name="yarsha_admin_advertisements_list")
     */
    public function indexAction(Request $request)
    {
        $filters = $request->query->all();

        $advertisementService = $this->get('yarsha.service.advertisement');

        $form = $this->createForm(AdvertisementType::class, new Advertisement());


        if( "POST" == $request->getMethod() ){

            $form->handleRequest($request);
            if($form->isValid())
            {
                try{
                    $advertisement = $form->getData();
                    $advertisement->upload();
                    $advertisementService->getEntityManager()->persist($advertisement);
                    $advertisementService->getEntityManager()->flush();
                    $this->addFlash('success', 'Advertisement added successfully.');
                    return $this->redirectToRoute('yarsha_admin_advertisements_list');
                }catch(\Exception $e){
                    $data['errorMessage'] = $e->getMessage();
                    $data['gotError'] = true;
                }
            }else{
                $data['gotError'] = true;
            }

        }

        $data['form'] = $form->createView();
        $data['advertisements'] = $advertisementService->getAdvertisementsPaginatedList($filters);
        return $this->render('@YarshaAdmin/Advertisement/index.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("ajax/advertisement/change/status", name="yarsha_admin_ajax_advertisement_change_status")
     */
    public function changeStatusAction(Request $request)
    {
        $id = $request->get('id');
        $advertisementService = $this->get('yarsha.service.advertisement');
        $advertisement = $advertisementService->findAdvertisementById($id);

        if(!$advertisement){
            return new JsonResponse(['message'=>'Advertisement not found.'], 404);
        }
        try{

            $advertisement->setStatus(! $advertisement->getStatus());
            $advertisementService->getEntityManager()->persist($advertisement);
            $advertisementService->getEntityManager()->flush();
        }catch(\Exception $e){
            return new JsonResponse(['message'=>$e->getMessage()], 500);
        }
        return new JsonResponse([]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("ajax/advertisement/{id}/delete", name="yarsha_admin_ajax_advertisement_delete")
     */
    public function deleteAction(Request $request)
    {
        $id = $request->get('id');
        $advertisementService = $this->get('yarsha.service.advertisement');
        $advertisement = $advertisementService->findAdvertisementById($id);

        if(!$advertisement){
            return new JsonResponse(['message'=>'Advertisement not found.'], 404);
        }

        try{
            $advertisement->setDeleted(true);
            $advertisementService->getEntityManager()->persist($advertisement);
            $advertisementService->getEntityManager()->flush();
            $this->addFlash('success', 'Advertisement Deleted Successfully.');
        }catch(\Exception $e){
            $this->addFlash('error', $e->getMessage());
        }

        return new JsonResponse([]);
    }
}
