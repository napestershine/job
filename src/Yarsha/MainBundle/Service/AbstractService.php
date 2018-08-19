<?php

namespace Yarsha\MainBundle\Service;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;

//use Share24\Bundle\LogsBundle\Service\LogsService;
//use Share24\Bundle\SettingBundle\Service\SettingService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Yarsha\EmployerBundle\Service\EmployerService;
use Yarsha\OrganizationBundle\Service\OrganizationService;

/**
 * Class AbstractService
 * @package Yarsha\MainBundle\Service
 *
 * @DI\Service("yarsha.service.abstract", abstract=true)
 */
class AbstractService
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var PaginationService
     */
    private $paginationService;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @param TokenStorage $tokenStorage
     */
    public function setTokenStorage(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return TokenStorage
     */
    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return PaginationService
     */
    public function getPaginationService()
    {
        return $this->paginationService;
    }

    /**
     * @param PaginationService $paginationService
     */
    public function setPaginationService($paginationService)
    {
        $this->paginationService = $paginationService;
    }


    /**
     * @param $class
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository($class)
    {
        return $this->getEntityManager()->getRepository($class);
    }

    /**
     * @param $entity
     */
    public function persist($entity)
    {
        $this->entityManager->persist($entity);
    }

    /**
     * @param $entity
     */
    public function flush($entity = null)
    {
        ($entity == null) ? $this->entityManager->flush() : $this->entityManager->flush($entity);
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }

}
