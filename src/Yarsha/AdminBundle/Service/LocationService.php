<?php

namespace Yarsha\AdminBundle\Service;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\EmployerBundle\Entity\User;
use Yarsha\EmployerBundle\Service\EmployerService;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\MainBundle\Entity\Country;
use Yarsha\MainBundle\Entity\Location;
use Yarsha\MainBundle\Service\AbstractService;
use Yarsha\OrganizationBundle\Entity\Organization;
use Yarsha\OrganizationBundle\Entity\OrganizationContactPerson;
use Yarsha\OrganizationBundle\OrganizationConstants;

/**
 * Class OrginazationService
 * @package Yarsha\OrganizationBundle\Service
 *
 * @DI\Service("yarsha.service.location", parent="yarsha.service.abstract")
 * @DI\Tag("yarsha.abstract_service")
 */
class LocationService extends AbstractService
{


    public function getPaginatedCountryList($filters = [])
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(Country::class)->getCountryListQueryBuilder($filters)
        );
    }

    public function getPaginatedLocationList($filters = [])
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(Location::class)->getLocationListQueryBuilder($filters)
        );
    }

    public function getCountryById($id)
    {
        return $this->getEntityManager()->getRepository(Country::class)->find($id);
    }

    public function getLocationsByCountry($filters)
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(Location::class)->getLocationListByCountryId($filters));
    }

    public function getLocationById($id)
    {
        return $this->getEntityManager()->getRepository(Location::class)->find($id);
    }

    public function getLocationsForSelect($filters = [])
    {
        return $this->getEntityManager()->getRepository(Location::class)->getLocationsForSelect($filters);
    }


}
