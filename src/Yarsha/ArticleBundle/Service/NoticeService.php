<?php

namespace Yarsha\ArticleBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\ArticleBundle\Entity\Notice;
use Yarsha\MainBundle\Service\AbstractService;

/**
 * Class NoticeService
 * @package Yarsha\ArticleBundle\Service
 * @DI\Service("yarsha.service.notice", parent="yarsha.service.abstract")
 * @DI\Tag(name = "yarsha.abstract_service")
 */
class NoticeService extends AbstractService
{

    public function getNoticeById($id)
    {
        return $this->getEntityManager()->getRepository(Notice::class)->find($id);
    }

    public function saveNotice($organization)
    {
        $em = $this->getEntityManager();
        $em->persist($organization);
        $em->flush();
    }

    public function getPaginatedNotices($filters = [], $userType = '')
    {
        $userId = $this->getUser()->getId();
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(Notice::class)->getAll($userId, $userType, $filters)
        );
    }

    public function getNoticesForSeeker($seekerID, $limit = null)
    {
        return $this->getEntityManager()->getRepository(Notice::class)
            ->getNoticesForSeekerQueryBuilder($seekerID, $limit)
            ->getQuery()->getResult();
    }

    public function getPaginatedNoticesForSeeker($seekerID)
    {
        $qb = $this->getEntityManager()->getRepository(Notice::class)->getNoticesForSeekerQueryBuilder($seekerID);
        return $this->getPaginationService()->getPagerFanta($qb);
    }

}
