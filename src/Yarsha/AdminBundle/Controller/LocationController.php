<?php
/**
 * Created by PhpStorm.
 * User: zone
 * Date: 2/14/17
 * Time: 12:31 PM
 */

namespace Yarsha\AdminBundle\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class LocationController
 * @package Yarsha\AdminBundle\Controller
 *
 * @Breadcrumb("Countries", routeName="yarsha_admin_country_list")
 */
class LocationController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("admin/country/list", name="yarsha_admin_country_list")
     */
    public function countryListAction(Request $request)
    {
        $filters = $request->query->all();

        $data['countries'] = $this->get('yarsha.service.location')->getPaginatedCountryList($filters);;

        return $this->render('@YarshaAdmin/Location/list.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("admin/country/{id}/locations", name="yarsha_admin_location_list")
     */
    public function locationListAction(Request $request)
    {
        $countryService = $this->get('yarsha.service.location');
        $id = $request->get('id');
        $data['country'] = $countryService->getCountryById($id);
        if(!$data['country']) throw new NotFoundHttpException;
        $filters = $request->query->all();

        $filters['id'] = $id;

        $data['locations'] = $countryService->getPaginatedLocationList($filters);;


        return $this->render('@YarshaAdmin/Location/location_list.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("admin/ajax/country/delete", name="yarsha_admin_ajax_country_delete")
     */
    public function deleteCountryAction(Request $request)
    {
        if('POST' != $request->getMethod()) return new JsonResponse(['message' => 'Invalid Method'], 403);
        $id = $request->get('country');
        $country = $this->get('yarsha.service.location')->getCountryById($id);
        if(!$country) return new JsonResponse(['message' => 'Country not found.'], 404);
        try{
            $country->setDeleted(true);
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($country);
            $em->flush();
            $this->addFlash('success', 'Country deleted successfully.');
        }catch(\Exception $e){return new JsonResponse(['message' => $e->getMessage()], 500); }
        return new JsonResponse([]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("admin/ajax/location/delete", name="yarsha_admin_ajax_location_delete")
     */
    public function deleteLocationAction(Request $request)
    {
        if('POST' != $request->getMethod()) return new JsonResponse(['message' => 'Invalid Method'], 403);
        $id = $request->get('location');
        $location = $this->get('yarsha.service.location')->getLocationById($id);
        if(!$location) return new JsonResponse(['message' => 'Location not found.'], 404);
        try{
            $location->setDeleted(true);
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($location);
            $em->flush();
            $this->addFlash('success', 'Location deleted successfully.');
        }catch(\Exception $e){return new JsonResponse(['message' => $e->getMessage()], 500); }
        return new JsonResponse([]);
    }


}
