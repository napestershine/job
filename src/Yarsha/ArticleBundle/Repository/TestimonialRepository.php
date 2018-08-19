<?php

namespace Yarsha\ArticleBundle\Repository;

use Yarsha\ArticleBundle\Entity\Testimonial;
use Yarsha\MainBundle\MainBundleConstants;


/**
 * TestimonialRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TestimonialRepository extends \Doctrine\ORM\EntityRepository
{

    public function getTestimonialList($filters)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('t')
            ->from(Testimonial::class, 't')
            ->where('t.isDeleted = 0');

        if (array_key_exists('title', $filters) and $filters['title'] != "") {
            $qb->andWhere(
                $qb->expr()->like('t.title', $qb->expr()->literal("%" . $filters['title'] . "%"))
            );
        }

        return $qb;
    }


    public function getTestimonialListByEmployer($filters, $userId)
    {


        $qb = $this->_em->createQueryBuilder();
        $qb->select('t')
            ->from(Testimonial::class, 't')
            ->where('t.isDeleted = 0');
        $qb->andWhere('t.userType = :userType')
            ->setParameter('userType', MainBundleConstants::USER_TYPE_EMPLOYER);
        $qb->andWhere('t.userId = :userId')
            ->setParameter('userId', $userId);

        if (array_key_exists('title', $filters) and $filters['title'] != "") {
            $qb->andWhere(
                $qb->expr()->like('t.title', $qb->expr()->literal("%" . $filters['title'] . "%"))
            );
        }

        if (array_key_exists('status', $filters) and $filters['status'] != "") {
            $qb->andWhere('t.status = :status')->setParameter('status', $filters['status']);
        }

        return $qb;
    }


}
