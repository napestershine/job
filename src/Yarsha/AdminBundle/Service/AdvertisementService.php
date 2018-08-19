<?php

namespace Yarsha\AdminBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\AdminBundle\Entity\Advertisement;
use Yarsha\MainBundle\Service\AbstractService;

/**
 * Class AdvertisementService
 * @package Yarsha\AdminBundle\Service
 *
 * @DI\Service("yarsha.service.advertisement", parent="yarsha.service.abstract")
 * @DI\Tag("yarsha.abstract_service")
 */
class AdvertisementService extends AbstractService
{
    public function findAdvertisementById($id)
    {
        return $this->getEntityManager()->getRepository(Advertisement::class)->find($id);
    }

    public function getAdvertisementsPaginatedList($filters = [])
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(Advertisement::class)->getAdvertisementsListQueryBuilder($filters)
        );
    }

}
