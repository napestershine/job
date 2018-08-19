<?php

namespace Yarsha\TagsBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AjaxController extends Controller
{
    /**
     * @Route("/ajax/tags/list", name="yarsha_ajax_tags_list")
     * @param Request $request
     * @return JsonResponse
     */
    public function searchTagsAction(Request $request)
    {
        if( ! $request->isXmlHttpRequest() )
        {
            return new JsonResponse(['error'=>'Bad Request'], 404);
        }

        $query = $request->get('q');

        $tags = $this->get('yarsha.service.tags')->getTagsList($query);

        $response = [];

        if( count($tags) )
        {
            foreach($tags as $tag )
            {
                $response[] = $tag['name'];
            }
        }

        return new JsonResponse($response);
    }

}
