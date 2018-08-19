<?php

namespace Yarsha\OrganizationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Yarsha\MainBundle\Entity\Category;
use Yarsha\OrganizationBundle\OrganizationConstants;

class AjaxController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/ajax/organization/change-status", name="yarsha_ajax_change_organization_status")
     */
    public function indexAction(Request $request)
    {
        $organizationId = $request->get('org');
        $nextStatus = $request->get('status');

        $orgService = $this->get('yarsha.service.organization');

        try {
            $organization = $orgService->changeStatus($nextStatus, $organizationId);
            $currentStatus = $organization->getStatus();
            $buttons = $this->get('yarsha.twig.organization_admin')
                ->getChangeStatusButtons($currentStatus, $organizationId);

            return new JsonResponse([
                'status' => $currentStatus,
                'buttons' => $buttons,
//                'label' => OrganizationConstants::$organizationStatus[$currentStatus],
                'label' => $this->get('yarsha.twig.organization_admin')->orgStatusLabel($currentStatus),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/ajax/organization/update/category", name="yarsha_ajax_update_organization_category")
     */
    public function updateCategoryAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $type = $request->get('type');
        $orgId = $request->get('orgId');
        $organization = $this->get('yarsha.service.organization')->getOrganizationById($orgId);

        $organization->setCategoryType($type);
        $em->persist($organization);

        try {
            $em->flush();
            $response['message'] = 'Category updated';
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }

        return new JsonResponse($response);
    }
}
