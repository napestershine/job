<?php

namespace Yarsha\MainBundle\Service;

use Yarsha\MainBundle\Entity\Emails;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class EmailService
 * @package Yarsha\MainBundle\Service
 *
 * @DI\Service("yarsha.service.email", parent="yarsha.service.abstract")
 * @DI\Tag("yarsha.abstract_service")
 */
class EmailService extends AbstractService
{

    public function getEmailById($id)
    {
        return $this->getEntityManager()->getRepository(Emails::class)->find($id);
    }



    public function getPaginatedEmails($filters = [])
    {
        $qb = $this->getEntityManager()
            ->getRepository(Emails::class)
            ->getAllEmails($filters);

        return $this->getPaginationService()->getPagerFanta($qb);
    }


}
