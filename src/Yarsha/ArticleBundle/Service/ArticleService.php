<?php

namespace Yarsha\ArticleBundle\Service;


use Yarsha\AdminBundle\Entity\User;
use Yarsha\ArticleBundle\Entity\Article;
use Yarsha\MainBundle\MainBundleConstants;
use Yarsha\MainBundle\Service\AbstractService;
use JMS\DiExtraBundle\Annotation as DI;


/**
 * Class ArticleService
 * @package Yarsha\ArticleBundle\Service
 *
 * @DI\Service("yarsha.service.article", parent="yarsha.service.abstract")
 * @DI\Tag("yarsha.abstract_service")
 */
class ArticleService extends AbstractService
{

    public function getArticleNewsById($id)
    {
        return $this->getEntityManager()->getRepository(Article::class)->find($id);
    }

    public function getArticleById($id)
    {
        return $this->getEntityManager()->getRepository(Article::class)->find($id);
    }

    public function getArticleBySlug($slug){
        return $this->getEntityManager()->getRepository(Article::class)->findOneBy([
            'slug' => $slug
        ]);
    }

    public function getPaginatedArticleList($filters = [])
    {

        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(Article::class)->getArticleList($filters)
        );
    }

    public function flushArticle(Article $article, $isUpdating = false)
    {
        $user = $this->getTokenStorage()->getToken()->getUser();

        if (!$isUpdating) {
            $article->setUser($user);
        }

        $this->getEntityManager()->persist($article);
        $this->getEntityManager()->flush();
    }

    public function hasPermissionToUpdatePost(Article $article)
    {
        $user = $this->getTokenStorage()->getToken()->getUser();

        if (!$user) {
            return false;
        }

        if ($user->hasRole('ROLE_ADMIN') ) {
            return true;
        }

        return false;
    }

    public function getUserType($user)
    {

        if ($user instanceof \Yarsha\EmployerBundle\Entity\User) {
            $type = MainBundleConstants::USER_TYPE_EMPLOYER;
        } elseif ($user instanceof \Yarsha\JobSeekerBundle\Entity\User) {
            $type = MainBundleConstants::USER_TYPE_EMPLOYEE;
        } else {
            $type = MainBundleConstants::USER_TYPE_ADMIN;
        }

        return $type;
    }


}
