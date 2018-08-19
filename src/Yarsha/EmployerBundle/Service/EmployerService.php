<?php

namespace Yarsha\EmployerBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\EmployerBundle\Entity\User;
use Yarsha\MainBundle\Service\AbstractService;
use Yarsha\OrganizationBundle\Entity\OrganizationBannerImages;
use Yarsha\OrganizationBundle\Entity\Organization;


/**
 * Class EmployerService
 * @package Yarsha\EmployerBundle\Service
 *
 * @DI\Service("yarsha.service.employer", parent="yarsha.service.abstract")
 * @DI\Tag("yarsha.abstract_service")
 */
class EmployerService extends AbstractService
{

    public function getEmployerById($id)
    {
        return $this->getEntityManager()->getRepository(User::class)->find($id);
    }

    public function getOrganizationBySlug($slug)
    {
        return $this->getEntityManager()->getRepository(Organization::class)->findOneBy([ 'slug' => $slug ]);
    }

    public function getPaginatedTotalApplicant($id)
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(user::class)->getTotalApplicantByEmployer($id)
        );
    }


    public function getPaginatedsearchResume($id, $filters)
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(user::class)->getSearchResume($id, $filters)
        );

    }

    public function getPaginatedAppliedResume($id, $filters)
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(user::class)->getResumeFromAppliedJob($id, $filters)
        );

    }


    public function getTotalJob($id)
    {
        return $this->getEntityManager()->getRepository(User::class)->getTotalJobPosted($id);

    }

    public function getEmployerBtId($id)
    {

        return $this->getEntityManager()->getRepository(user::class)->find($id);
    }

    public function getTotalFollower($id)
    {
        return $this->getEntityManager()->getRepository(User::class)->getTotalFollowerByEmployer($id);
    }

    public function getActiveBanner($id)
    {
        return $this->getEntityManager()->getRepository(OrganizationBannerImages::class)->findBy(

            [
                'status' => 1,
                'employer' => $id
            ],
            ['order' => 'ASC']

        );
    }

    public function getActiveBannerByLimit($id)
    {
        return $this->getEntityManager()->getRepository(OrganizationBannerImages::class)->getBanners($id, 5);
    }


    public function getRelatedCompany($categoryId, $empId)
    {

        return $this->getEntityManager()->getRepository(User::class)->related_company($categoryId, $empId);

    }

    public function getRelatedCompanyJobs($categoryId, $orgId)
    {

        return $this->getEntityManager()->getRepository(User::class)->related_company_jobs($categoryId, $orgId);

    }


    public function getJobsofEmployer($empId, $limit = null)
    {

        return $this->getEntityManager()->getRepository(User::class)->getJobsbyEmployer($empId, $limit);

    }

    public function getEmployerByOrganization($organization)
    {
        return $this->getEntityManager()->getRepository(User::class)->findOneBy([
            'organization' => $organization
        ]);
    }

    public function getTopEmployers($limit = null)
    {
        $topEmployers = $this->getEntityManager()->getRepository(Organization::class)->getTopEmployers($limit);

        return $topEmployers;
    }



}
