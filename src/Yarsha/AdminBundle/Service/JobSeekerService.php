<?php
/**
 * Created by PhpStorm.
 * User: yarsha
 * Date: 2/2/17
 * Time: 12:29 PM
 */

namespace Yarsha\AdminBundle\Service;

use Yarsha\JobSeekerBundle\Entity\User;
use Yarsha\MainBundle\Service\AbstractService;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class ArticleService
 * @package Yarsha\ArticleBundle\Service
 *
 * @DI\Service("yarsha.admin.service.jobseeker", parent="yarsha.service.abstract")
 * @DI\Tag("yarsha.abstract_service")
 */
class JobSeekerService extends AbstractService
{

    public function getJobSeekerById($id)
    {
        return $this->getEntityManager()->getRepository(User::class)->find($id);
    }

    public function getPaginatedJobSeekers($filters = [])
    {

        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(User::class)->getAllUsers($filters)
        );
    }

    public function getPaginatedAppliedJobs($id, $filters = [])
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(User::class)->getAllAppliedJobs($id, $filters)
        );
    }

    public function getPaginatedAppliedCompanies($id, $filters = [])
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(User::class)->getAllAppliedCompanies($id, $filters)
        );
    }

    public function getPaginatedFollowedCompanies($id, $filters = [])
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(User::class)->getAllFollowedCompanies($id, $filters)
        );
    }

    public function getPaginatedFollowedOrganization($id, $filters = [])
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(User::class)->getAllFollowedOrganization($id, $filters)
        );
    }


    public function getPaginatedJobSeekersBySearchText($searchText)
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(User::class)->getUserBySearchText($searchText)
        );
    }

    public function getCallsById($id)
    {
        return $this->getEntityManager()->getRepository(JobSeekerCallRecord::class)->find($id);
    }


}

