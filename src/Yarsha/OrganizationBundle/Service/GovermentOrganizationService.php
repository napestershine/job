<?php

namespace Yarsha\OrganizationBundle\Service;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\EmployerBundle\Entity\User;
use Yarsha\EmployerBundle\Service\EmployerService;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\MainBundle\Service\AbstractService;
use Yarsha\OrganizationBundle\Entity\Organization;
use Yarsha\OrganizationBundle\Entity\OrganizationContactPerson;
use Yarsha\OrganizationBundle\OrganizationConstants;

/**
 * Class GovermentOrganizationService
 * @package Yarsha\OrganizationBundle\Service
 *
 * @DI\Service("yarsha.service.government_organization", parent="yarsha.service.abstract")
 * @DI\Tag("yarsha.abstract_service")
 */
class GovermentOrganizationService extends AbstractService
{

    /**
     * @var EmployerService
     */
    private $employerService;

    /**
     * OrganizationService constructor.
     * @param EmployerService $employerService
     *
     * @DI\InjectParams({"employerService"= @DI\Inject("yarsha.service.employer")})
     */
    public function __construct(EmployerService $employerService)
    {
        $this->employerService = $employerService;
    }


    public function getGovermentOrganizationsPaginatedList($filters = [])
    {
        $qb = $this->getEntityManager()->getRepository(Organization::class)
            ->getGovermentOrganizationListQueryBuilder($filters);

        return $this->getPaginationService()->getPagerFanta($qb);
    }

}
