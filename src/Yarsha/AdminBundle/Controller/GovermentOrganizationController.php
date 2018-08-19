<?php

namespace Yarsha\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yarsha\AdminBundle\Form\GovermentOrganizationFormType;
use Yarsha\OrganizationBundle\Entity\Organization;
use Symfony\Component\HttpFoundation\JsonResponse;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Class NewspaperOrganizationController
 * @package Yarsha\AdminBundle\Controller
 *
 * @Route("/admin")
 * @Breadcrumb("Goverment Organization",routeName="yarsha_admin_goverment_organization_list")
 */
class GovermentOrganizationController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/goverment-organization", name="yarsha_admin_goverment_organization_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->query->all();

        $data['organizations'] = $this
            ->get('yarsha.service.government_organization')
            ->getGovermentOrganizationsPaginatedList($filters);

        return $this->render("YarshaAdminBundle:GovermentOrganization:list.html.twig", $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/goverment-organization/new", name="yarsha_admin_goverment_organization_new")
     * @Route("/goverment-organization/{id}/update", name="yarsha_admin_goverment_organization_update")
     */
    public function createAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data['isUpdating'] = false;

        $id = $request->get('id');

        if ($id) {
            $organization = $this->get('yarsha.service.organization')->getOrganizationById($id);

            if (!$id and !$organization->isIsGovermentOrganization()) {
                throw new NotFoundHttpException;
            }

            $data['isUpdating'] = true;

        } else {
            $organization = new Organization();
        }

        $form = $this->createForm(GovermentOrganizationFormType::class, $organization);

        $form->handleRequest($request);

        $this->get('apy_breadcrumb_trail')->add($data['isUpdating'] ? $organization->getName() : 'New');

        if ($form->isSubmitted() and $form->isValid()) {
            $organization = $form->getData();

            try {
                $organization->setIsGovermentOrganization(true);
                $organization->upload();
                $em = $this->get('doctrine.orm.entity_manager');
                $em->persist($organization);
                $em->flush();
                $this->addFlash('success', 'Organization Saved Successfully,');

                return $this->redirectToRoute('yarsha_admin_goverment_organization_list');
            } catch (\Exception $e) {
                $data['errorMessage'] = $e->getMessage();
            }
        }

        $data['form'] = $form->createView();

        return $this->render("YarshaAdminBundle:GovermentOrganization:create.html.twig", $data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/goverment-organization/{id}/delete", name="yarsha_admin_goverment_organization_delete")
     */
    public function deleteAction(Request $request)
    {

        $orgId = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $organizationRepo = $em->getRepository('YarshaOrganizationBundle:Organization');
        $organization = $organizationRepo->find($orgId);
        $response = [];
        if ($organization) {
            $organization->deActivate();
            $this->getDoctrine()->getEntityManager()->persist($organization);
            try {
                $this->getDoctrine()->getEntityManager()->flush();
                $response['status'] = 'success';
            } catch (\Exception $e) {
                $response['status'] = $e->getMessage();
            }
        }

        return new JsonResponse($response);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @Route("/ajax/goverment-organization/{id}/view", name="yarsha_goverment_organization_ajax_view")
     */
    public function viewGovOrganizationAction($id)
    {
        $org = $this->getDoctrine()->getManager()->getRepository(Organization::class)->find($id);
        $data['govOrganization'] = $org;
        if (!$org) {
            $data['success'] = false;
        } else {
            $data['success'] = true;
            $data['organizationdata'] = $this->renderView('YarshaAdminBundle:GovermentOrganization:view.html.twig',
                $data);
        }

        return new JsonResponse($data);
    }


}
