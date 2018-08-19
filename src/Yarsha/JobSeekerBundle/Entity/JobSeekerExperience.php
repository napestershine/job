<?php

namespace Yarsha\JobSeekerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Yarsha\JobsBundle\Entity\JobLevel;
use Yarsha\MainBundle\Entity\Country;
use Yarsha\OrganizationBundle\Entity\Organization;
use Yarsha\OrganizationBundle\Entity\OrganizationType;

/**
 * JobSeekerExperience
 *
 * @ORM\Table(name="ys_job_seeker_experiences")
 * @ORM\Entity(repositoryClass="Yarsha\JobSeekerBundle\Repository\JobSeekerExperienceRepository")
 */
class JobSeekerExperience
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
     * @ORM\Column(name="organization_name", type="string", length=255)
     */
    private $organizationName;

    /**
     * @var string
     *
     * @ORM\Column(name="employment_type", type="string", length=255)
     */
    private $employmentType;

    /**
     * @var OrganizationType
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\MainBundle\Entity\Category")
     */
    private $organizationType;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=255)
     */
    private $designation;

    /**
     * @var JobLevel
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\JobsBundle\Entity\JobLevel")
     */
    private $jobLevel;

    /**
     * @var int
     *
     * @ORM\Column(name="from_year", type="integer", length=4)
     */
    private $fromYear;

    /**
     * @var int
     *
     * @ORM\Column(name="from_month", type="string")
     */
    private $fromMonth;

    /**
     * @var int
     *
     * @ORM\Column(name="from_day", type="integer", length=2, nullable=true)
     */
    private $fromDay;


    /**
     * @var int
     *
     * @ORM\Column(name="to_year", type="integer", nullable=true, length=4, nullable=true)
     */
    private $toYear;

    /**
     * @var int
     *
     * @ORM\Column(name="to_month", type="string", nullable=true)
     */
    private $toMonth;

    /**
     * @var int
     *
     * @ORM\Column(name="to_day", type="integer", length=2, nullable=true)
     */
    private $toDay;

    /**
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\MainBundle\Entity\Country")
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="text", nullable=true)
     */
    private $roles;


    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\JobSeekerBundle\Entity\User")
     */
    private $jobSeeker;

    /**
     * @var boolean
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted = false;


    /**
     * @var boolean
     * @ORM\Column(name="Currently_working", type="boolean", nullable=true)
     */
    private $currentlyWorking = false;


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
     * Set organizationName
     *
     * @param string $organizationName
     *
     * @return JobSeekerExperience
     */
    public function setOrganizationName($organizationName)
    {
        $this->organizationName = $organizationName;

        return $this;
    }

    /**
     * Get organizationName
     *
     * @return string
     */
    public function getOrganizationName()
    {
        return $this->organizationName;
    }

    /**
     * Set employmentType
     *
     * @param string $employmentType
     *
     * @return JobSeekerExperience
     */
    public function setEmploymentType($employmentType)
    {
        $this->employmentType = $employmentType;

        return $this;
    }

    /**
     * Get employmentType
     *
     * @return string
     */
    public function getEmploymentType()
    {
        return $this->employmentType;
    }

    /**
     * Set organizationType
     *
     * @param string $organizationType
     *
     * @return JobSeekerExperience
     */
    public function setOrganizationType($organizationType)
    {
        $this->organizationType = $organizationType;

        return $this;
    }

    /**
     * Get organizationType
     *
     * @return string
     */
    public function getOrganizationType()
    {
        return $this->organizationType;
    }

    /**
     * Set designation
     *
     * @param string $designation
     *
     * @return JobSeekerExperience
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
     * Set jobLevel
     *
     * @param string $jobLevel
     *
     * @return JobSeekerExperience
     */
    public function setJobLevel($jobLevel)
    {
        $this->jobLevel = $jobLevel;

        return $this;
    }

    /**
     * Get jobLevel
     *
     * @return string
     */
    public function getJobLevel()
    {
        return $this->jobLevel;
    }

    /**
     * Set fromYear
     *
     * @param integer $fromYear
     *
     * @return JobSeekerExperience
     */
    public function setFromYear($fromYear)
    {
        $this->fromYear = $fromYear;

        return $this;
    }

    /**
     * Get fromYear
     *
     * @return int
     */
    public function getFromYear()
    {
        return $this->fromYear;
    }

    /**
     * Set fromMonth
     *
     * @param integer $fromMonth
     *
     * @return JobSeekerExperience
     */
    public function setFromMonth($fromMonth)
    {
        $this->fromMonth = $fromMonth;

        return $this;
    }

    /**
     * Get fromMonth
     *
     * @return int
     */
    public function getFromMonth()
    {
        return $this->fromMonth;
    }

    /**
     * Set toYear
     *
     * @param integer $toYear
     *
     * @return JobSeekerExperience
     */
    public function setToYear($toYear)
    {
        $this->toYear = $toYear;

        return $this;
    }

    /**
     * Get toYear
     *
     * @return int
     */
    public function getToYear()
    {
        return $this->toYear;
    }

    /**
     * Set toMonth
     *
     * @param integer $toMonth
     *
     * @return JobSeekerExperience
     */
    public function setToMonth($toMonth)
    {
        $this->toMonth = $toMonth;

        return $this;
    }

    /**
     * Get toMonth
     *
     * @return int
     */
    public function getToMonth()
    {
        return $this->toMonth;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return JobSeekerExperience
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set roles
     *
     * @param string $roles
     *
     * @return JobSeekerExperience
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return string
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @return User
     */
    public function getJobSeeker()
    {
        return $this->jobSeeker;
    }

    /**
     * @param User $jobSeeker
     *
     * @return JobSeekerExperience
     */
    public function setJobSeeker($jobSeeker)
    {
        $this->jobSeeker = $jobSeeker;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     *
     * @return JobSeekerExperience
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * @return int
     */
    public function getFromDay()
    {
        return $this->fromDay;
    }

    /**
     * @param int $fromDay
     */
    public function setFromDay($fromDay)
    {
        $this->fromDay = $fromDay;
    }

    /**
     * @return int
     */
    public function getToDay()
    {
        return $this->toDay;
    }

    /**
     * @param int $toDay
     */
    public function setToDay($toDay)
    {
        $this->toDay = $toDay;
    }

    /**
     * @return boolean
     */
    public function isCurrentlyWorking()
    {
        return $this->currentlyWorking;
    }

    /**
     * @param boolean $currentlyWorking
     */
    public function setCurrentlyWorking($currentlyWorking)
    {
        $this->currentlyWorking = $currentlyWorking;
    }


}

