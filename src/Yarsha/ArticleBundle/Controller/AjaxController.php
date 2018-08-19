<?php

namespace Yarsha\ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yarsha\ArticleBundle\Entity\Testimonial;
use Yarsha\ArticleBundle\Entity\Article;

class AjaxController extends Controller
{

    /**
     * @param $id
     * @return JsonResponse
     * @Route("/ajax/testimonial/{id}/view", name="yarsha_article_ajax_view")
     */
    public function viewTestimonialAction($id)
    {
        $testimonial = $this->getDoctrine()->getManager()->getRepository(Testimonial::class)->find($id);
        $data['testimonial'] = $testimonial;
        if (!$testimonial) {
            $data['success'] = false;
        } else {
            $data['success'] = true;
            $data['testimonialdata'] = $this->renderView('YarshaArticleBundle:Testimonial:view.html.twig', $data);
        }

        return new JsonResponse($data);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @Route("/ajax/article/{id}/view", name="yarsha_articles_ajax_view")
     */
    public function viewArticleAction($id)
    {
        $article = $this->getDoctrine()->getManager()->getRepository(Article::class)->find($id);
        $data['article'] = $article;
        if (!$article) {
            $data['success'] = false;
        } else {
            $data['success'] = true;
            $data['articledata'] = $this->renderView('YarshaArticleBundle:Article:view.html.twig', $data);
        }

        return new JsonResponse($data);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @Route("/ajax/news/{id}/view", name="yarsha_news_ajax_view")
     */
    public function viewNewsAction($id)
    {
        $news = $this->getDoctrine()->getManager()->getRepository(Article::class)->find($id);
        $data['article'] = $news;
        if (!$news) {
            $data['success'] = false;
        } else {
            $data['success'] = true;
            $data['newsdata'] = $this->renderView('YarshaArticleBundle:Article:view.html.twig', $data);
        }

        return new JsonResponse($data);
    }
}
