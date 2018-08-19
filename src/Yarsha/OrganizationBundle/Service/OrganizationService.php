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
 * Class OrginazationService
 * @package Yarsha\OrganizationBundle\Service
 *
 * @DI\Service("yarsha.service.organization", parent="yarsha.service.abstract")
 * @DI\Tag("yarsha.abstract_service")
 */
class OrganizationService extends AbstractService
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

    public function getOrganizationsForSelect()
    {
        return $this->getEntityManager()->getRepository(Organization::class)->getOrganizationsForSelect();
    }

    public function getOrganizationById($id)
    {
        return $this->getEntityManager()->getRepository(Organization::class)->find($id);
    }

    public function getPaginatedOrganizationList($filters = [])
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(Organization::class)->getOrganizationList($filters)
        );
    }

    public function changeStatus($status, $id)
    {
        $organization = $this->getOrganizationById($id);

        if (!$organization) {
            throw new \Exception('Organization Not Found.', 404);
        }

        $oldStatus = $organization->getStatus();

        if (!$this->canPerformStatusChange($oldStatus, $status)) {
            throw new \Exception('Unable to change status.', 400);
        }

        if (
            $oldStatus == OrganizationConstants::ORGANIZATION_STATUS_PENDING
            and $status == OrganizationConstants::ORGANIZATION_STATUS_APPROVED
        ) {
            $employers = $this->getEntityManager()
                ->getRepository(User::class)->findBy(['organization' => $organization]);

            foreach ($employers as $employer) {
                $employer->setEnabled(true);
                $this->getEntityManager()->persist($employer);
            }

        }

        $organization->setStatus($status);
        $this->getEntityManager()->persist($organization);

        $this->getEntityManager()->flush();

        return $organization;
    }

    public function statusMappings($status = '')
    {
        $mappings = [
            OrganizationConstants::ORGANIZATION_STATUS_PENDING => [
                OrganizationConstants::ORGANIZATION_STATUS_DISABLED,
                OrganizationConstants::ORGANIZATION_STATUS_APPROVED
            ],
            OrganizationConstants::ORGANIZATION_STATUS_APPROVED => [
                OrganizationConstants::ORGANIZATION_STATUS_DISABLED
            ],
            OrganizationConstants::ORGANIZATION_STATUS_DISABLED => [
                OrganizationConstants::ORGANIZATION_STATUS_APPROVED
            ]
        ];

        if ($status == "") {
            return $mappings;
        }

        return ($status != "" and isset($mappings[$status])) ? $mappings[$status] : [];
    }

    public function canPerformStatusChange($oldStatus, $newStatus)
    {
        $mappings = $this->statusMappings();


        return (isset($mappings[$oldStatus]) and in_array($newStatus, $mappings[$oldStatus]))
            ? true
            : false;
    }

    public function getContactPersonByOrganizationId($id)
    {
        return $this->getEntityManager()->getRepository(OrganizationContactPerson::class)
            ->findBy(['organization' => $id, 'status' => 1]);
    }

    public function getContactPersonDetailsByOrganizationId($id)
    {
        return $this->getEntityManager()->getRepository(OrganizationContactPerson::class)
            ->findOneBy([
                'organization' => $id,
                'status' => 1,
                'contactType' => OrganizationContactPerson::CONTACT_TYPE_MAIN_CONTACT
            ]);
    }

    public function getContactEmail($organizationId)
    {
        $cp = $this->getContactPersonDetailsByOrganizationId($organizationId);

        return $cp ? $cp->getEmail() : '';
    }

    public function getUserIdByOrganization($id)
    {
        return $this->getEntityManager()->getRepository(User::class)->findOneBy(['organization' => $id]);
    }

    public function getJobListByOrganizationId($id)
    {
        return $this->getEntityManager()->getRepository(Job::class)->findBy(['organization' => $id]);
    }

    public function getSuperEmployers($limit = null)
    {
        return $this->getEntityManager()->getRepository(Organization::class)->getSuperEmployers($limit);
    }

    public function getTopAndSuperEmployers($limit = null)
    {
        return $this->getEntityManager()->getRepository(Organization::class)->getTopAndSuperEmployers($limit);
    }

    public function getTopEmployers($limit = null)
    {
        return $this->getEntityManager()->getRepository(Organization::class)->getTopEmployers($limit);
    }

    public function getHiringEmployers($limit = null)
    {
        return $this->getEntityManager()->getRepository(Organization::class)->getHiringEmployers($limit);
    }

    public function getPaginatedJobListByOrganization($organizationId)
    {
        $filters['organization'] = $organizationId;

        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(Job::class)->getJobList($filters)
        );
    }

    public function getFeaturedEmployers($limit = null)
    {
        $topEmployers = $this->getEntityManager()->getRepository(Organization::class)->getFeaturedEmployers($limit);

        return $topEmployers;
    }

}
