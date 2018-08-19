<?php

namespace Yarsha\JobsBundle\Service;

use Yarsha\JobsBundle\Entity\Job;
use Yarsha\JobsBundle\JobConstants;
use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\JobsBundle\Entity\JobApplied;
use Yarsha\MainBundle\Service\AbstractService;


/**
 * Class JobService
 * @DI\Service("yarsha.service.newspaper_job", parent="yarsha.service.abstract")
 * @DI\Tag("yarsha.abstract_service")
 */
class NewspaperJobService extends AbstractService
{

    public function getNewspaperJobsPaginatedList($filters = [])
    {
        $queryBuilder = $this->getEntityManager()
            ->getRepository(Job::class)
            ->getNewspaperJobsListQueryBuilder($filters);

        return $this->getPaginationService()->getPagerFanta($queryBuilder);
    }

}
