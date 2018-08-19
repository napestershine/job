<?php
/**
 * Created by PhpStorm.
 * User: zone
 * Date: 1/31/17
 * Time: 5:30 PM
 */

namespace Yarsha\ArticleBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Yarsha\ArticleBundle\Entity\Article;
use Yarsha\ArticleBundle\Form\ArticleType;
use Yarsha\MainBundle\MainBundleEvents;
use Yarsha\TagsBundle\Event\PostEvent;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Class ArticleController
 * @package Yarsha\ArticleBundle\Controller
 * @Breadcrumb("Articles",routeName="yarsha_admin_article_list")
 */
class ArticleController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/admin/articles/list", name="yarsha_admin_article_list")
     */
    public function indexAction(Request $request)
    {
//        $articles = $this->getDoctrine()->getRepository(Article::class)->findBy([
//            'type' => Article::ARTICLE_TYPE_ARTICLE,
//            'deleted' => 0
//        ]);


        $filters = $request->query->all();
        $filters['type'] = Article::ARTICLE_TYPE_ARTICLE;
        $articleService = $this->get('yarsha.service.article');
        $articles = $articleService->getPaginatedArticleList($filters);

        $data['articles'] = $articles;

        return $this->render('YarshaArticleBundle:Article:list.html.twig', $data);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/admin/article/create", name="yarsha_admin_article_create")
     * @Route("/admin/article/{id}/update", name="yarsha_admin_article_update")
     */
    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $id = $request->get('id');

        $articleService = $this->get('yarsha.service.article');

        if ($id) {
            $article = $articleService->getArticleById($id);

            if (!$article) {
                throw new NotFoundHttpException;
            }

//            if (!$articleService->hasPermissionToUpdatePost($article)) {
//                throw new AccessDeniedException;
//            }

            $isUpdating = true;
            $tags = implode(",", $this->get('yarsha.service.tags')->getTags($article, false));

        } else {
            $article = new Article();
            $isUpdating = false;
            $tags = "";
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->get('tags')->setData($tags);

        $form->handleRequest($request);
        $this->get('apy_breadcrumb_trail')->add($isUpdating ? $article->getTitle() : 'New');

        if ($form->isSubmitted() and $form->isValid()) {
            $postData = $form->getData();

            try {
                $postData->setType(Article::ARTICLE_TYPE_ARTICLE);
                $articleService->flushArticle($postData, $isUpdating);
                $dispatcher = $this->get('event_dispatcher');
                $postEvent = new PostEvent($postData, $form);
                $dispatcher->dispatch(MainBundleEvents::ADD_EDIT_POST, $postEvent);

                return $this->redirectToRoute('yarsha_admin_article_list');
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
        $data['updating'] = $isUpdating;

        $data['form'] = $form->createView();

        return $this->render('YarshaArticleBundle:Article:create_article.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/admin/article/{id}/delete", name="yarsha_admin_article_delete")
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

        return $this->redirectToRoute('yarsha_admin_article_list');

    }

}
