<?php

namespace Yarsha\ArticleBundle\Service;


use Yarsha\AdminBundle\Entity\User;
use Yarsha\ArticleBundle\Entity\Testimonial;
use Yarsha\MainBundle\MainBundleConstants;
use Yarsha\MainBundle\Service\AbstractService;
use JMS\DiExtraBundle\Annotation as DI;


/**
 * Class TestimonialService
 * @package Yarsha\ArticleBundle\Service
 *
 * @DI\Service("yarsha.service.Testimonial", parent="yarsha.service.abstract")
 * @DI\Tag("yarsha.abstract_service")
 */
class TestimonialService extends AbstractService
{

    public function getTestimonialNewsById($id)
    {
        return $this->getEntityManager()->getRepository(Testimonial::class)->find($id);
    }

    public function getTestimonialById($id)
    {
        return $this->getEntityManager()->getRepository(Testimonial::class)->find($id);
    }



    public function getPaginatedTestimonialList($filters = [])
    {

        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(Testimonial::class)->getTestimonialList($filters)
        );
    }

    public function getPaginatedEmployerTestimonialList($filters = [])
    {

        $user = $this->getTokenStorage()->getToken()->getUser();
        $userId = $user->getId();
        return $this->getPaginationService()->getPagerFanta(
            $this->getEntityManager()->getRepository(Testimonial::class)->getTestimonialListByEmployer($filters,$userId)
        );
    }

    public function flushTestimonial(Testimonial $testimonial, $isUpdating = false)
    {
        $user = $this->getTokenStorage()->getToken()->getUser();

        $userId = $user->getId();

        if (!$isUpdating) {
            $testimonial->setUserId($userId);
            $testimonial->setUserType($this->getUserType($user));
        }
        $testimonial->upload();

        $this->getEntityManager()->persist($testimonial);
        $this->getEntityManager()->flush();
    }

    public function hasPermissionToUpdatePost(Testimonial $article)
    {
        $user = $this->getTokenStorage()->getToken()->getUser();

        if (!$user) {
            return false;
        }

        if ($user->hasRole('ROLE_SUPER_ADMIN') or
            ($article->getUserId() == $user->getId() and $article->getUserType() == $this->getUserType($user))
        ) {
            return true;
        }

        return false;
    }

    public function getUserType($user)
    {

        if ($user instanceof \Yarsha\EmployerBundle\Entity\User) {
            $type = MainBundleConstants::USER_TYPE_EMPLOYER;
        } elseif ($user instanceof \Yarsha\JobSeekerBundle\Entity\User) {
            $type = MainBundleConstants::USER_TYPE_EMPLOYEE;
        } else {
            $type = MainBundleConstants::USER_TYPE_ADMIN;
        }

        return $type;
    }


}
