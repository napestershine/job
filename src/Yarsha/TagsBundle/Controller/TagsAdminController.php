<?php

namespace Yarsha\TagsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Yarsha\TagsBundle\Form\TagType;
use Yarsha\TagsBundle\Event\TagEvent;
use Yarsha\TagsBundle\Event\PostEvent;
use Yarsha\MainBundle\MainBundleEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * @Route("/admin")
 * Class TagsController
 * @package Yarsha\TagsBundle\Controller
 */
class TagsAdminController extends Controller
{
    /**
     * @Route("/tags", name="yarsha_admin_tags_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $filters = $request->query->all();

        $service = $this->get('yarsha.service.tags');

        $form = $this->createForm(TagType::class, null, ['add' => true]);

        if( "POST" == $request->getMethod() )
        {
            $form->handleRequest($request);

            if($form->isValid())
            {
                $tag = $form->getData();

                try{
                    $service->flush($tag);
                    $this->addFlash('success', 'Tag Added Successfully.');
                    return $this->redirectToRoute('yarsha_admin_tags_list');
                }catch(\Exception $e)
                {
                    $data['error'] = $e->getMessage();
                }
            }
        }

        $data['pageTitle'] = 'Tags';
        $data['pageDescription'] = "Contents' Tags";
        $data['tags'] = $service->getPaginatedTagsList($filters);
        $data['popularTags'] = $service->getPopularTags(10);
        $data['form'] = $form->createView();

        return $this->render('YarshaTagsBundle:Admin:index.html.twig', $data);
    }

    /**
     * @Route("/tags/update/{id}", name="yarsha_admin_tags_update")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tagFormAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $id = $request->get('id');

        $service = $this->get('yarsha.service.tags');

        $tag = $service->getTagById($id);

        if(! $tag) throw new NotFoundHttpException;

        $form = $this->createForm(TagType::class, $tag);

        if( "POST" == $request->getMethod() )
        {
            $form->handleRequest($request);

            if($form->isValid())
            {
                $tag = $form->getData();

                try{
                    $dispatcher = $this->get('event_dispatcher');

                    $tagEvent = new PostEvent($tag, $form);

                    $dispatcher->dispatch(MainBundleEvents::EVENT_UPDATE_TAG, $tagEvent);

                    $this->addFlash('success', 'Tags Updated Successfully.');

                    return $this->redirectToRoute("yarsha_admin_tags_list");

                }catch(\Exception $e)
                {
                    $data['error'] = $e->getMessage();
                }
            }
        }

        $data['pageTitle'] = 'Tags';
        $data['pageDescription'] = $tag->getName();
        $data['form'] = $form->createView();

        return $this->render('YarshaTagsBundle:Admin:form.html.twig', $data);
    }

    /**
     * @Route("/tags/delete/{id}", name="yarsha_admin_tags_delete")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tagDeleteAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $id = $request->get('id');

        $service = $this->get('yarsha.service.tags');

        $tag = $service->getTagById($id);

        if(! $tag) throw new NotFoundHttpException;

        try{
            $dispatcher = $this->get('event_dispatcher');

            $tagEvent = new TagEvent($tag);

            $dispatcher->dispatch(MainBundleEvents::EVENT_DELETE_TAG, $tagEvent);

            $this->addFlash('success', 'Tags Deleted Successfully.');

            return $this->redirectToRoute("yarsha_admin_tags_list");

        }catch(\Exception $e)
        {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute("yarsha_admin_tags_list");
        }
    }
}

