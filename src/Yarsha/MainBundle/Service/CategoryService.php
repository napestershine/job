<?php

namespace Yarsha\MainBundle\Service;

use Yarsha\MainBundle\Entity\Category;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class CategoryService
 * @package Yarsha\MainBundle\Service
 *
 * @DI\Service("yarsha.service.job_category", parent="yarsha.service.abstract")
 * @DI\Tag("yarsha.abstract_service")
 */
class CategoryService extends AbstractService
{

    public function getCategoryById($id)
    {
        return $this->getEntityManager()->getRepository(Category::class)->find($id);
    }

    public function getPaginatedCategoriesList($filters = [])
    {
        $qb = $this->getEntityManager()
            ->getRepository(Category::class)
            ->getAllCategories($filters);

        return $this->getPaginationService()->getPagerFanta($qb);
    }

    public function getPaginatedSearchCategories($searchText)
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getRepository(Category::class)->searchCategoriesBySearchText($searchText)
        );
    }

    public function deleteCategory(Category $category)
    {
        $em = $this->getEntityManager();
        $category->setDeleted(true);
        $em->persist($category);
        $em->flush();
    }

    public function saveCategory(Category $category)
    {
        if (!$category->getCreatedBy()) {
            $user = $this->getTokenStorage()->getToken()->getUser();
            $category->setCreatedBy($user);
        }

        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();
    }

    public function getCategoriesForSelect($type = '')
    {
        return $this->getEntityManager()->getRepository(Category::class)->getCategoriesForSelect($type);
    }
}
