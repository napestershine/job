<?php

namespace Yarsha\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Yarsha\AdminBundle\Form\EducationDegreeType;
use Yarsha\MainBundle\Entity\EducationDegree;

/**
 * Class EducationDegreeController
 * @package Yarsha\AdminBundle\Controller
 *
 * @Route("/admin")
 */
class EducationDegreeController extends Controller
{


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/education-degree", name="yarsha_admin_education_degree")
     */
    public function indexAction(Request $request)
    {
        $data['degrees'] = $this
            ->get('doctrine.orm.entity_manager')
            ->getRepository(EducationDegree::class)
            ->getAll()
        ;

        return $this->render('YarshaAdminBundle:EducationDegree:index.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/ajax/education-degree/new", name="yarsha_admin_ajax_education_degree_create")
     * @Route("/ajax/education-degree/{id}/update", name="yarsha_admin_ajax_education_degree_update")
     */
    public function createAction(Request $request)
    {
        $id = $request->get('id');
        $response['status'] = true;
        $data['id'] = '';
        if($id){
            $em = $this->get('doctrine.orm.entity_manager');
            $degree = $em->getRepository(EducationDegree::class)->find($id);
            $data['id'] = $degree->getId();
        }   else    {
            $degree = new EducationDegree();
        }

        $form = $this->createForm(EducationDegreeType::class, $degree, []);
        $form->handleRequest($request);

        if("POST" == $request->getMethod() and $form->isValid())
        {
            $degree = $form->getData();
            try{
                $em = $this->get('doctrine.orm.entity_manager');
                $em->persist($degree);
                $em->flush();
            }
            catch(\Exception $e){
                $response['status'] = false;
                $response['message'] = $e->getMessage();
            }
        }

        $data['form'] = $form->createView();
        $response['template'] = $this->renderView('YarshaAdminBundle:EducationDegree:create.html.twig', $data);

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/ajax/educationDegree/{id}/delete", name="yarsha_admin_ajax_education_degree_delete")
     */
    public function deleteEducationDegree(Request $request){
        $id = $request->get('id');
        $response['success'] = false;
        if($id){
            $em = $this->get('doctrine.orm.entity_manager');
            $degree = $em->getRepository(EducationDegree::class)->find($id);
            if($degree){
                $degree->setDeleted(true);
                $em->persist($degree);
                try{
                    $em->flush();
                    $response['success'] = true;
                    $response['message'] = "One education degree deleted.";
                }   catch(\Exception $e){
                    $response['message'] = "Error Message: ".$e->getMessage();
                }
            }
        }
        return new JsonResponse($response);
    }
}
