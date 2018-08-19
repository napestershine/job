<?php

namespace Yarsha\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Yarsha\JobsBundle\Entity\JobLevel;
use Yarsha\JobsBundle\Form\JobLevelType;

class JobLevelController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/joblevel/list", name="yarsha_admin_job_level_list")
     */
    public function listJobLevelAction(){
        $data['jobLevels'] = $this->getDoctrine()->getManager()->getRepository(JobLevel::class)->getAllJobLevels();
        return $this->render('@YarshaAdmin/JobLevel/listjoblevels.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/ajax/admin/job-level/new", name="yarsha_admin_ajax_job_level_create")
     * @Route("/ajax/admin/job-level/{id}/update", name="yarsha_admin_ajax_job_level_update")
     */
    public function createAction(Request $request)
    {
        $id = $request->get('id');
        $response['status'] = true;
        $formData = null;

        if($id){
            $formData = $this->getDoctrine()->getManager()->getRepository(JobLevel::class)->find($id);
            $data['levelId'] = $id;
        }

        $form = $this->createForm(JobLevelType::class, $formData, []);
        $form->handleRequest($request);

        if("POST" == $request->getMethod() and $form->isValid())
        {
            $joblevel = $form->getData();
            try{
                $em = $this->get('doctrine.orm.entity_manager');
                $em->persist($joblevel);
                $em->flush();
                $this->addFlash('success', 'Job Level Added Successfully.');
            }
            catch(\Exception $e){
                $response['status'] = false;
                $response['message'] = $e->getMessage();
            }
        }

        $data['form'] = $form->createView();
        $response['template'] = $this->renderView('YarshaAdminBundle:JobLevel:addjoblevel.html.twig', $data);

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/ajax/admin/joblevel/{id}/delete", name="yarsha_ajax_admin_job_level_delete")
     */
    public function deleteJobLevel(Request $request){
        $id = $request->get('id');
        $data['success'] = false;
        $em = $this->getDoctrine()->getManager();
        $jobLevel = $em->getRepository(JobLevel::class)->find($id);
        if($jobLevel){
            $jobLevel->setDeleted(true);
            $em->persist($jobLevel);
            try{
                $em->flush();
                $data['success'] = true;
            }   catch (\Exception $e){
                $data['success'] = false;
                $data['message'] = $e->getMessage();
            }
        }
        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/ajax/admin/joblevel/sort", name="yarsha_ajax_admin_job_level_sort")
     */
    public function sortJobLevel(Request $request){
        $em = $this->getDoctrine()->getManager();
        $data['success'] = false;
        $data['errorMessage'] = false;
        $data = $request->get('item');
        $counter = 1;
        foreach ($data as $position){
            $jobLevel = $em->getRepository(JobLevel::class)->find($position);
            $jobLevel->setSortOrder($counter);
            $em->persist($jobLevel);
            $counter++;
        }

        try{
            $em->flush();
            $data['success'] = true;
        } catch (Exception $e){
            $data['errorMessage'] = "unable to sort";
        }
        return new JsonResponse($data);
    }
}
