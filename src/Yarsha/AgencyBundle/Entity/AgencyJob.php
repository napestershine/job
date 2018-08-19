<?php

namespace Yarsha\AgencyBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class AgencyJob
 *
 * @ORM\Table(name="ys_agency_jobs")
 * @ORM\Entity(repositoryClass="Yarsha\AgencyBundle\Repository\AgencyJobRepository")
 */
class AgencyJob
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
     * @var User
     * @ORM\ManyToOne(targetEntity="Yarsha\AgencyBundle\Entity\User")
     */
    private $agency;

    /**
     * @var string
     *
     * @ORM\Column(name="job_reference", type="string", length=255, nullable=true)
     */
    private $jobReference;

    /**
     * @var string
     *
     * @ORM\Column(name="job_title", type="string", length=255, nullable=false)
     */
    private $jobTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="job_type", type="string", length=255, nullable=true)
     */
    private $jobType;


    /**
     * @var string
     *
     * @ORM\Column(name="job_duration", type="string", length=255, nullable=true)
     */
    private $jobDuration;

    /**
     * @var string
     *
     * @ORM\Column(name="job_start_date", type="string", length=255, nullable=true)
     */
    private $jobStartDate;


    /**
     * @var string
     *
     * @ORM\Column(name="job_skills", type="text",  nullable=true)
     */
    private $jobSkills;

    /**
     * @var string
     *
     * @ORM\Column(name="job_description", type="text", nullable=true)
     */
    private $jobDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="job_location", type="string", length=255, nullable=true)
     */
    private $jobLocation;

    /**
     * @var string
     *
     * @ORM\Column(name="job_industry", type="string", length=255, nullable=true)
     */
    private $jobIndustry;

    /**
     * @var string
     *
     * @ORM\Column(name="salary_currency", type="string", length=255, nullable=true)
     */
    private $salaryCurrency;

    /**
     * @var string
     *
     * @ORM\Column(name="salary_from", type="string", length=255, nullable=true)
     */
    private $salaryFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="salary_to", type="string", length=255, nullable=true)
     */
    private $salaryTo;

    /**
     * @var string
     *
     * @ORM\Column(name="salary_per", type="string", length=255, nullable=true)
     */
    private $salaryPer;

    /**
     * @var string
     *
     * @ORM\Column(name="salary_benefits", type="text",  nullable=true)
     */
    private $salaryBenefits;


    /**
     * @var string
     *
     * @ORM\Column(name="salary", type="string", length=255, nullable=true)
     */
    private $salary;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_date", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedDate;

    /**
     * @var boolean
     * @ORM\Column(name="deleted",type="boolean", length=1)
     */

    private $deleted = false;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getJobReference()
    {
        return $this->jobReference;
    }

    /**
     * @param string $jobReference
     */
    public function setJobReference($jobReference)
    {
        $this->jobReference = $jobReference;
    }

    /**
     * @return string
     */
    public function getJobTitle()
    {
        return $this->jobTitle;
    }

    /**
     * @param string $jobTitle
     */
    public function setJobTitle($jobTitle)
    {
        $this->jobTitle = $jobTitle;
    }

    /**
     * @return string
     */
    public function getJobType()
    {
        return $this->jobType;
    }

    /**
     * @param string $jobType
     */
    public function setJobType($jobType)
    {
        $this->jobType = $jobType;
    }

    /**
     * @return string
     */
    public function getJobDuration()
    {
        return $this->jobDuration;
    }

    /**
     * @param string $jobDuration
     */
    public function setJobDuration($jobDuration)
    {
        $this->jobDuration = $jobDuration;
    }

    /**
     * @return string
     */
    public function getJobStartDate()
    {
        return $this->jobStartDate;
    }

    /**
     * @param string $jobStartDate
     */
    public function setJobStartDate($jobStartDate)
    {
        $this->jobStartDate = $jobStartDate;
    }

    /**
     * @return string
     */
    public function getJobSkills()
    {
        return $this->jobSkills;
    }

    /**
     * @param string $jobSkills
     */
    public function setJobSkills($jobSkills)
    {
        $this->jobSkills = $jobSkills;
    }

    /**
     * @return string
     */
    public function getJobDescription()
    {
        return $this->jobDescription;
    }

    /**
     * @param string $jobDescription
     */
    public function setJobDescription($jobDescription)
    {
        $this->jobDescription = $jobDescription;
    }

    /**
     * @return string
     */
    public function getJobLocation()
    {
        return $this->jobLocation;
    }

    /**
     * @param string $jobLocation
     */
    public function setJobLocation($jobLocation)
    {
        $this->jobLocation = $jobLocation;
    }

    /**
     * @return string
     */
    public function getJobIndustry()
    {
        return $this->jobIndustry;
    }

    /**
     * @param string $jobIndustry
     */
    public function setJobIndustry($jobIndustry)
    {
        $this->jobIndustry = $jobIndustry;
    }

    /**
     * @return string
     */
    public function getSalaryCurrency()
    {
        return $this->salaryCurrency;
    }

    /**
     * @param string $salaryCurrency
     */
    public function setSalaryCurrency($salaryCurrency)
    {
        $this->salaryCurrency = $salaryCurrency;
    }

    /**
     * @return string
     */
    public function getSalaryFrom()
    {
        return $this->salaryFrom;
    }

    /**
     * @param string $salaryFrom
     */
    public function setSalaryFrom($salaryFrom)
    {
        $this->salaryFrom = $salaryFrom;
    }

    /**
     * @return string
     */
    public function getSalaryTo()
    {
        return $this->salaryTo;
    }

    /**
     * @param string $salaryTo
     */
    public function setSalaryTo($salaryTo)
    {
        $this->salaryTo = $salaryTo;
    }

    /**
     * @return string
     */
    public function getSalaryPer()
    {
        return $this->salaryPer;
    }

    /**
     * @param string $salaryPer
     */
    public function setSalaryPer($salaryPer)
    {
        $this->salaryPer = $salaryPer;
    }

    /**
     * @return string
     */
    public function getSalaryBenefits()
    {
        return $this->salaryBenefits;
    }

    /**
     * @param string $salaryBenefits
     */
    public function setSalaryBenefits($salaryBenefits)
    {
        $this->salaryBenefits = $salaryBenefits;
    }

    /**
     * @return string
     */
    public function getSalary()
    {
        return $this->salary;
    }

    /**
     * @param string $salary
     */
    public function setSalary($salary)
    {
        $this->salary = $salary;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @param \DateTime $createdDate
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    /**
     * @param \DateTime $updatedDate
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return mixed
     */
    public function getAgency()
    {
        return $this->agency;
    }

    /**
     * @param mixed $agency
     */
    public function setAgency($agency)
    {
        $this->agency = $agency;
    }





}

