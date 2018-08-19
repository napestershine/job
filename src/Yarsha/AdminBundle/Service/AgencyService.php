<?php

namespace Yarsha\AdminBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\AgencyBundle\Entity\AgencyJob;
use Yarsha\AgencyBundle\Entity\User;
use Yarsha\MainBundle\Service\AbstractService;


/**
 * Class AgencyService
 * @package Yarsha\AdminBundle\Service
 *
 * @DI\Service("yarsha.service.admin_agency", parent="yarsha.service.abstract")
 * @DI\Tag("yarsha.abstract_service")
 */
class AgencyService extends AbstractService
{
    public function getPaginatedAgencyList($filters = [])
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(user::class)->getAllAgencies($filters)
        );
    }

    public function getAgencyById($id)
    {
        return $this->getEntityManager()->getRepository(user::class)->find($id);
    }

    public function getPaginatedAgencyJobList($filters = [])
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(AgencyJob::class)->getAllAgenciesJobs($filters)
        );
    }

    public function getAgencyJobById($id)
    {
        return $this->getEntityManager()->getRepository(AgencyJob::class)->find($id);
    }

}
