<?php

namespace Yarsha\JobSeekerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JobSeekerReference
 *
 * @ORM\Table(name="ys_job_seeker_references")
 * @ORM\Entity(repositoryClass="Yarsha\JobSeekerBundle\Repository\JobSeekerReferenceRepository")
 */
class JobSeekerReference
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="organization", type="string", length=255)
     */
    private $organization;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=255)
     */
    private $designation;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="homePhone", type="string", length=255, nullable=true)
     */
    private $homePhone;

    /**
     * @var string
     *
     * @ORM\Column(name="officePhone", type="string", length=255, nullable=true)
     */
    private $officePhone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=255)
     */
    private $mobile;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\JobSeekerBundle\Entity\User", inversedBy="references", cascade={"persist"})
     */
    private $jobSeeker;



    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set organization
     *
     * @param string $organization
     *
     * @return JobSeekerReference
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return string
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set designation
     *
     * @param string $designation
     *
     * @return JobSeekerReference
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get designation
     *
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return JobSeekerReference
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return JobSeekerReference
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set homePhone
     *
     * @param string $homePhone
     *
     * @return JobSeekerReference
     */
    public function setHomePhone($homePhone)
    {
        $this->homePhone = $homePhone;

        return $this;
    }

    /**
     * Get homePhone
     *
     * @return string
     */
    public function getHomePhone()
    {
        return $this->homePhone;
    }

    /**
     * Set officePhone
     *
     * @param string $officePhone
     *
     * @return JobSeekerReference
     */
    public function setOfficePhone($officePhone)
    {
        $this->officePhone = $officePhone;

        return $this;
    }

    /**
     * Get officePhone
     *
     * @return string
     */
    public function getOfficePhone()
    {
        return $this->officePhone;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return JobSeekerReference
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     *
     * @return JobSeekerReference
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @return User
     */
    public function getJobSeeker()
    {
        return $this->jobSeeker;
    }

    /**
     * @param User 
     *
     * @return  JobSeekerReference
     */
    public function setJobSeeker($jobSeeker)
    {
        $this->jobSeeker = $jobSeeker;

        return $this;
    }
    
}

