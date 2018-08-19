<?php
/**
 * Created by PhpStorm.
 * User: zone
 * Date: 2/1/17
 * Time: 4:18 PM
 */

namespace Yarsha\ArticleBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yarsha\ArticleBundle\Entity\Article;
use Yarsha\ArticleBundle\Form\NewsType;
use Yarsha\MainBundle\MainBundleEvents;
use Yarsha\TagsBundle\Event\PostEvent;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Class NewsController
 * @package Yarsha\ArticleBundle\Controller
 * @Breadcrumb("News",routeName="yarsha_admin_news_list")
 */
class NewsController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/admin/news/list", name="yarsha_admin_news_list")
     */
    public function indexAction()
    {
//        $data['news'] = $this->getDoctrine()->getRepository(Article::class)
//            ->findBy(['type' => Article::ARTICLE_TYPE_NEWS, 'deleted' => 0]);
        $articleService = $this->get('yarsha.service.article');
        $filters['type'] = Article::ARTICLE_TYPE_NEWS;
        $data['news'] = $articleService->getPaginatedArticleList($filters);

        return $this->render('YarshaArticleBundle:News:list.html.twig', $data);

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/admin/news/create", name="yarsha_admin_news_create")
     * @Route("/admin/news/{id}/update", name="yarsha_admin_news_update")
     */
    public function addAction(Request $request)
    {

        $id = $request->get('id');


        $articleService = $this->get('yarsha.service.article');

        if ($id) {
            $article = $articleService->getArticleById($id);

            if (!$article) {
                throw new NotFoundHttpException;
            }

            if (!$articleService->hasPermissionToUpdatePost($article)) {
                throw new AccessDeniedException;
            }

            $isUpdating = true;
            $tags = implode(",", $this->get('yarsha.service.tags')->getTags($article, false));

        } else {
            $article = new Article();
            $isUpdating = false;
            $tags = "";
        }


        $form = $this->createForm(NewsType::class, $article);

        $form->get('tags')->setData($tags);

        $form->handleRequest($request);
        $this->get('apy_breadcrumb_trail')->add($isUpdating ? $article->getTitle() : 'New');

        if ($form->isSubmitted() and $form->isValid()) {
            $postData = $form->getData();


            try {
                $postData->setType(Article::ARTICLE_TYPE_NEWS);
                $postData->setCategory(Article::ARTICLE_CATEGORY_ALL);

                $articleService->flushArticle($postData, $isUpdating);

                $dispatcher = $this->get('event_dispatcher');
                $postEvent = new PostEvent($postData, $form);
                $dispatcher->dispatch(MainBundleEvents::ADD_EDIT_POST, $postEvent);

                return $this->redirectToRoute('yarsha_admin_news_list');
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
        $data['updating'] = $isUpdating;

        $data['form'] = $form->createView();

        return $this->render('YarshaArticleBundle:News:create_article.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/admin/news/{id}/delete", name="yarsha_admin_news_delete")
     */
    public function deleteAction(Request $request)
    {
        $id = $request->get('id');
        $articleService = $this->get('yarsha.service.article');
        $news = $articleService->getArticleNewsById($id);
        if (!$news) {
            return NotFoundHttpException::class;
        }
        $news->setDeleted(true);

        $articleService->persist($news);
        $articleService->flush();

        return $this->redirectToRoute('yarsha_admin_news_list');

    }

}
