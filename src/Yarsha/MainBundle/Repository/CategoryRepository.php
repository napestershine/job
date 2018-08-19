<?php

namespace Yarsha\MainBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Lexer;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\JobsBundle\JobConstants;
use Yarsha\MainBundle\Entity\Category;
use Yarsha\OrganizationBundle\Entity\Organization;

class CategoryRepository extends EntityRepository
{

    public function getJobsByCategorySection($sectionName, $limit = 30)
    {
        $limit = (is_numeric($limit) && $limit > 0) ? $limit : 30;
        $qb = $this->_em->createQueryBuilder();
        $results = $qb->select('c')
            ->from(Category::class, 'c')
            ->where('c.section = :section')
            ->setParameter('section', $sectionName)
            ->setMaxResults($limit)->getQuery()->getResult();

        return $results;
    }


//    public function findJobsCountByCategorySection($section, $limit = 30)
//    {
//        $qb = $this->_em->createQueryBuilder();
//
//        $qb->select(['count(j.id) as jobsCount', 'c.id', 'c.title', 'c.slug', 'c.section',])
//            ->from(Category::class, 'c');
//
//
//        $joinColumn = ($section == Category::CATEGORY_TYPE_JOB_BY_INDUSTRY) ? 'j.industry' : 'j.category';
//
//
//        $condition = $joinColumn . " = c.id AND j.status = " . JobConstants::JOB_STATUS_APPROVED;
//        $condition .= " j.deadline >= '" . date('Y-m-d') . "'";
//        $condition .= " AND j.jobsFrom != '" . JobConstants::JOB_FROM_GOVERNMENT . "'";
//
//        $qb->leftJoin(Job::class, 'j', 'WITH', $condition)
//            ->where('c.section = :section')->setParameter('section', $section)
//            ->andWhere('c.deleted = 0')
//            ->groupBy('c.id')
//            ->orderBy('jobsCount', 'desc')
//            ->setMaxResults($limit);
//
//        return $qb->getQuery()->getArrayResult();
//    }


    public function findJobsCountByCategorySection($section, $limit = 30)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select(['count(j.id) as jobsCount', 'c.id', 'c.title', 'c.slug', 'c.section',])
            ->from(Category::class, 'c');


        $joinColumn = ($section == Category::CATEGORY_TYPE_JOB_BY_INDUSTRY) ? 'org.industry' : 'j.category';

        if ($section == Category::CATEGORY_TYPE_JOB_BY_INDUSTRY) {
            $conditionByIndustry = $joinColumn . " = c.id ";
            $condition = " j.deadline >= '" . date('Y-m-d') . "'";
            $condition .= " AND j.status = '" . JobConstants::JOB_STATUS_APPROVED . "'";
            $condition .= " AND j.jobsFrom != '" . JobConstants::JOB_FROM_GOVERNMENT . "'";
        } else {
            $condition = $joinColumn . " = c.id AND j.status = " . JobConstants::JOB_STATUS_APPROVED;
            $condition .= " AND j.deadline >= '" . date('Y-m-d') . "'";
            $condition .= " AND j.jobsFrom != '" . JobConstants::JOB_FROM_GOVERNMENT . "'";
        }

        $qb->leftJoin(Job::class, 'j', 'WITH', $condition);
        if ($section == Category::CATEGORY_TYPE_JOB_BY_INDUSTRY) {
            $qb->innerJoin('j.organization', 'org', 'WITH', $conditionByIndustry);
        }
        $qb->where('c.section = :section')->setParameter('section', $section)
            ->andWhere('c.deleted = 0')
            ->groupBy('c.id')
            ->orderBy('jobsCount', 'desc')
            ->setMaxResults($limit);

        return $qb->getQuery()->getArrayResult();
    }

    public function getAllCategories($filters = [], $limit = 40)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('c')
            ->from(Category::class, 'c')
            ->where("c.deleted != 1");

        if (array_key_exists('title', $filters) and $filters['title'] != "") {
            $qb->andWhere(
                $qb->expr()->like(
                    'c.title',
                    $qb->expr()->literal("%" . $filters['title'] . "%")
                )
            );
        }

        if (array_key_exists('section', $filters) and $filters['section'] != "") {
            $qb->andWhere('c.section = :section')
                ->setParameter('section', $filters['section']);
        }

        return $qb;
    }

    public function getCategoriesForSelect($type = "")
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select(['c.id', 'c.title'])
            ->from(Category::class, 'c')
            ->where('c.deleted = 0');

        if ($type != "") {
            $qb->andWhere('c.section = :section')->setParameter('section', $type);
        }

        return $qb->getQuery()->getArrayResult();
    }

}
