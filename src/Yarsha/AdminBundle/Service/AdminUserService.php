<?php

namespace Yarsha\AdminBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Sonata\AdminBundle\Annotation\Admin;
use Yarsha\MainBundle\Service\AbstractService;
use Yarsha\AdminBundle\Entity\User as AdminUser;

/**
 * Class AdminUserService
 * @package Yarsha\ArticleBundle\Service
 *
 * @DI\Service("yarsha.service.admin_user", parent="yarsha.service.abstract")
 * @DI\Tag("yarsha.abstract_service")
 */
class AdminUserService extends AbstractService
{

    public function getAdminUserById($id)
    {
        return $this->getEntityManager()->getRepository(AdminUser::class)->find($id);
    }

    public function getPaginatedAccountManagerList($filters = [])
    {
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(AdminUser::class)->getAccountManagersQueryBuilder($filters)
        );
    }


}

