<?php

namespace Yarsha\EmployerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Yarsha\OrganizationBundle\Entity\Organization;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Yarsha\JobSeekerBundle\Entity\User as JobSeeker;

/**
 * Class User
 * @package Yarsha\EmployerBundle\Entity
 *
 * @ORM\Entity(repositoryClass="Yarsha\EmployerBundle\Repository\UserRepository")
 * @ORM\Table(name="ys_employers")
 * @UniqueEntity("username")
 */
class User extends BaseUser
{

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    protected $id;

    /**
     * @var Organization
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\OrganizationBundle\Entity\Organization")
     */
    private $organization;

    /**
     * @ORM\ManyToMany(targetEntity="Yarsha\JobSeekerBundle\Entity\User")
     * @ORM\JoinTable(name="ys_employer_followers",
     *      joinColumns={@ORM\JoinColumn(name="employer_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="follower_id", referencedColumnName="id")}
     * )
     */
    private $followers;


    public function __construct()
    {
        parent::__construct();
        $this->followers = new ArrayCollection();
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param Organization $organization
     *
     * @return User
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @param JobSeeker $follower
     */
    public function addFollower(JobSeeker $follower)
    {
        $this->followers[] = $follower;
    }

    public function removeFollower($follower)
    {
        $this->followers->removeElement($follower);
    }

    /**
     * @return ArrayCollection
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * @param ArrayCollection $followers
     */
    public function setFollowers($followers)
    {
        $this->followers = $followers;
    }


}
