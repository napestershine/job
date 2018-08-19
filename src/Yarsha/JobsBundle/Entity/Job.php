<?php

namespace Yarsha\JobsBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Yarsha\JobsBundle\JobConstants;
use Yarsha\MainBundle\Entity\Currency;
use Gedmo\Mapping\Annotation as Gedmo;
use Yarsha\MainBundle\Entity\Category;
use Yarsha\EmployerBundle\Entity\User;
use Yarsha\MainBundle\Entity\Location;
use Yarsha\MainBundle\Entity\EducationDegree;
use Doctrine\Common\Collections\ArrayCollection;
use Yarsha\OrganizationBundle\Entity\Organization;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use JMS\Serializer\Annotation\Expose;

/**
 * Class Job
 * @package Yarsha\JobsBundle\Entity\Job
 *
 * @ORM\Table(name="ys_jobs")
 * @ORM\Entity(repositoryClass="Yarsha\JobsBundle\Repository\JobRepository")
 */
class Job
{

    const JOBS_TYPE_FREE = 'free';

    const JOBS_TYPE_FEATURED = 'featured';

    const JOBS_TYPE_HOT = 'hot';

    const JOBS_TYPE_NEWSPAPER = 'newspaper';

    const JOBS_AVAILABILITY_FULL_TIME = 'full';

    const JOBS_AVAILABILITY_PART_TIME = 'part';

    const JOBS_AVAILABILITY_CONTRACT = 'contract';

    const JOBS_SALARY_TYPE_FIXED = 'fixed';

    const JOBS_SALARY_TYPE_NEGOTIABLE = 'negotiable';

    const JOBS_SALARY_TYPE_RANGE = 'range';


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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\MainBundle\Entity\Category")
     */
    private $category;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\MainBundle\Entity\Category")
     */
    private $industry;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=20)
     */
    private $type = JobConstants::JOBS_TYPE_FREE;


    /**
     * @var JobLevel
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\JobsBundle\Entity\JobLevel")
     */
    private $level;

    /**
     * @var string
     *
     * @ORM\Column(name="availability", type="string", length=20)
     */
    private $availability = JobConstants::JOBS_AVAILABILITY_FULL_TIME;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Yarsha\MainBundle\Entity\Location")
     * @ORM\JoinTable(name="ys_job_location")
     */
    private $locations;

    /**
     * @var int
     *
     * @ORM\Column(name="minimum_experience_year", type="integer", nullable=true)
     */
    private $minimumExperienceYear = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="maximum_experience_year", type="integer", nullable=true)
     */
    private $maximumExperienceYear = 0;

    /**
     * @var EducationDegree
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\MainBundle\Entity\EducationDegree")
     */
    private $educationDegree;

    /**
     * @var int
     *
     * @ORM\Column(name="number_of_vacancies", type="integer")
     */
    private $numberOfVacancies = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="vacancy_code", type="string", length=255, nullable=true)
     */
    private $vacancyCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadline", type="date")
     */
    private $deadline;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="specification", type="text", nullable=true)
     */
    private $specification;

    /**
     * @var string
     *
     * @ORM\Column(name="education_description", type="text", nullable=true)
     */
    private $educationDescription;

    /**
     * @var Currency
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\MainBundle\Entity\Currency")
     */
    private $salaryUnit;

    /**
     * @var bool
     *
     * @ORM\Column(name="salary_negotiable", type="boolean", nullable=true)
     */
    private $salaryNegotiable = true;

    /**
     * @var
     * @ORM\Column(name="salary_type", type="text", nullable=true)
     */
    private $salaryType;

    /**
     * @var
     * @ORM\Column(name="salary_payment_basis", type="text", nullable=true)
     */
    private $salaryPaymentBasis;

    /**
     * @var
     * @ORM\Column(name="salary", type="integer", nullable=true)
     */
    private $salary;

    /**
     * @var string
     *
     * @ORM\Column(name="minimum_salary", type="decimal", precision=10, scale=3, nullable=true)
     */
    private $minimumSalary;

    /**
     * @var string
     *
     * @ORM\Column(name="maximum_salary", type="decimal", precision=10, scale=3, nullable=true)
     */
    private $maximumSalary;

    /**
     * @var string
     *
     * @ORM\Column(name="preferred_gender", type="string", length=10, nullable=true)
     */
    private $preferredGender;

    /**
     * @var int
     *
     * @ORM\Column(name="minimum_age", type="integer", nullable=true)
     */
    private $minimumAge;

    /**
     * @var int
     *
     * @ORM\Column(name="maximum_age", type="integer", nullable=true)
     */
    private $maximumAge;

    /**
     * @var string
     *
     * @ORM\Column(name="specific_requirement", type="text", nullable=true)
     */
    private $specificRequirement;

    /**
     * @var string
     *
     * @ORM\Column(name="specific_instruction", type="text", nullable=true)
     */
    private $specificInstruction;

    /**
     * @var
     * @ORM\Column(name="postal_address", type="text", nullable=true)
     */
    private $postalAddress;

    /**
     * @var
     * @ORM\Column(name="online_link", type="text", nullable=true)
     */
    private $onlineLink;

    /**
     * @var JobSetting
     *
     * @ORM\OneToOne(targetEntity="Yarsha\JobsBundle\Entity\JobSetting", mappedBy="job", cascade={"persist"})
     */
    private $settings;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="jobs_from", type="string", length=20, nullable=true)
     *
     */
    private $jobsFrom = JobConstants::JOB_FROM_EMPLOYERS;

    /**
     * @var Organization
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\OrganizationBundle\Entity\Organization")
     */
    private $organization;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=100, nullable=true)
     */
    private $username;


    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", length=3, nullable=true)
     */
    private $status = JobConstants::JOB_STATUS_PENDING;


    /**
     * Image path
     *
     * @var string
     *
     * @ORM\Column(type="text", length=255, nullable=true)
     *
     * @Expose
     */
    protected $path;

    /**
     * Image file
     *
     * @var File
     * @Vich\UploadableField(mapping="", fileNameProperty="path")
     *
     * @Assert\File(
     *     maxSize = "50M",
     *     mimeTypes = {"application/*", "video/*", "audio/*", "image/*"},
     *     maxSizeMessage = "The maximum allowed file size is 50MB.",
     *     mimeTypesMessage = "Only the filetypes image are allowed."
     * )
     */
    protected $file;


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
     * @var
     *
     * @ORM\Column(name="view_count", type="bigint", nullable=true)
     */
    private $viewCount = 0;

    /**
     * @var String
     *
     * @ORM\Column(type="string", name="template", length=50, nullable=true)
     */
    private $template;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
    }

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
     * Set title
     *
     * @param string $title
     *
     * @return Job
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Job
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set category
     *
     * @param Category $category
     *
     * @return Job
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set industry
     *
     * @param Category $industry
     *
     * @return Job
     */
    public function setIndustry($industry)
    {
        $this->industry = $industry;

        return $this;
    }

    /**
     * Get industry
     *
     * @return Category
     */
    public function getIndustry()
    {
        return $this->industry;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Job
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set level
     *
     * @param JobLevel $level
     *
     * @return Job
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return JobLevel
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set availability
     *
     * @param string $availability
     *
     * @return Job
     */
    public function setAvailability($availability)
    {
        $this->availability = $availability;

        return $this;
    }

    /**
     * Get availability
     *
     * @return string
     */
    public function getAvailability()
    {
        return $this->availability;
    }

    /**
     * Add locations
     *
     * @param Location $location
     *
     * @return Job
     */
    public function addLocation($location)
    {
        $this->locations[] = $location;

        return $this;
    }

    /**
     * Remove location
     *
     * @param Location $location
     *
     * @return Job
     */
    public function removeLocation($location)
    {
        $this->locations->removeElement($location);

        return $this;
    }

    /**
     * Reset locations
     *
     * @return Job
     */
    public function resetLocations()
    {
        $this->locations = new ArrayCollection();

        return $this;
    }

    /**
     * Get locations
     *
     * @return ArrayCollection
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * Set minimumExperienceYear
     *
     * @param integer $minimumExperienceYear
     *
     * @return Job
     */
    public function setMinimumExperienceYear($minimumExperienceYear)
    {
        $this->minimumExperienceYear = $minimumExperienceYear;

        return $this;
    }

    /**
     * Get minimumExperienceYear
     *
     * @return int
     */
    public function getMinimumExperienceYear()
    {
        return $this->minimumExperienceYear;
    }

    /**
     * Set maximumExperienceYear
     *
     * @param integer $maximumExperienceYear
     *
     * @return Job
     */
    public function setMaximumExperienceYear($maximumExperienceYear)
    {
        $this->maximumExperienceYear = $maximumExperienceYear;

        return $this;
    }

    /**
     * Get maximumExperienceYear
     *
     * @return int
     */
    public function getMaximumExperienceYear()
    {
        return $this->maximumExperienceYear;
    }

    /**
     * Set educationDegree
     *
     * @param EducationDegree $educationDegree
     *
     * @return Job
     */
    public function setEducationDegree($educationDegree)
    {
        $this->educationDegree = $educationDegree;

        return $this;
    }

    /**
     * Get educationDegree
     *
     * @return EducationDegree
     */
    public function getEducationDegree()
    {
        return $this->educationDegree;
    }

    /**
     * Set numberOfVacancies
     *
     * @param integer $numberOfVacancies
     *
     * @return Job
     */
    public function setNumberOfVacancies($numberOfVacancies)
    {
        $this->numberOfVacancies = $numberOfVacancies;

        return $this;
    }

    /**
     * Get numberOfVacancies
     *
     * @return int
     */
    public function getNumberOfVacancies()
    {
        return $this->numberOfVacancies;
    }

    /**
     * Set vacancyCode
     *
     * @param string $vacancyCode
     *
     * @return Job
     */
    public function setVacancyCode($vacancyCode)
    {
        $this->vacancyCode = $vacancyCode;

        return $this;
    }

    /**
     * Get vacancyCode
     *
     * @return string
     */
    public function getVacancyCode()
    {
        return $this->vacancyCode;
    }

    /**
     * Set deadline
     *
     * @param \DateTime $deadline
     *
     * @return Job
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get deadline
     *
     * @return \DateTime
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Job
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set specification
     *
     * @param string $specification
     *
     * @return Job
     */
    public function setSpecification($specification)
    {
        $this->specification = $specification;

        return $this;
    }

    /**
     * Get specification
     *
     * @return string
     */
    public function getSpecification()
    {
        return $this->specification;
    }

    /**
     * Set educationDescription
     *
     * @param string $educationDescription
     *
     * @return Job
     */
    public function setEducationDescription($educationDescription)
    {
        $this->educationDescription = $educationDescription;

        return $this;
    }

    /**
     * Get educationDescription
     *
     * @return string
     */
    public function getEducationDescription()
    {
        return $this->educationDescription;
    }

    /**
     * Set salaryUnit
     *
     * @param string $salaryUnit
     *
     * @return Job
     */
    public function setSalaryUnit($salaryUnit)
    {
        $this->salaryUnit = $salaryUnit;

        return $this;
    }

    /**
     * Get salaryUnit
     *
     * @return string
     */
    public function getSalaryUnit()
    {
        return $this->salaryUnit;
    }

    /**
     * Set salaryNegotiable
     *
     * @param boolean $salaryNegotiable
     *
     * @return Job
     */
    public function setSalaryNegotiable($salaryNegotiable)
    {
        $this->salaryNegotiable = $salaryNegotiable;

        return $this;
    }

    /**
     * Get salaryNegotiable
     *
     * @return bool
     */
    public function getSalaryNegotiable()
    {
        return $this->salaryNegotiable;
    }

    /**
     * Set minimumSalary
     *
     * @param string $minimumSalary
     *
     * @return Job
     */
    public function setMinimumSalary($minimumSalary)
    {
        $this->minimumSalary = $minimumSalary;

        return $this;
    }

    /**
     * Get minimumSalary
     *
     * @return string
     */
    public function getMinimumSalary()
    {
        return $this->minimumSalary;
    }

    /**
     * Set maximumSalary
     *
     * @param string $maximumSalary
     *
     * @return Job
     */
    public function setMaximumSalary($maximumSalary)
    {
        $this->maximumSalary = $maximumSalary;

        return $this;
    }

    /**
     * Get maximumSalary
     *
     * @return string
     */
    public function getMaximumSalary()
    {
        return $this->maximumSalary;
    }

    /**
     * @return mixed
     */
    public function getSalaryType()
    {
        return $this->salaryType;
    }

    /**
     * @param mixed $salaryType
     */
    public function setSalaryType($salaryType)
    {
        $this->salaryType = $salaryType;
    }

    /**
     * @return mixed
     */
    public function getSalaryPaymentBasis()
    {
        return $this->salaryPaymentBasis;
    }

    /**
     * @param mixed $salaryPaymentBasis
     */
    public function setSalaryPaymentBasis($salaryPaymentBasis)
    {
        $this->salaryPaymentBasis = $salaryPaymentBasis;
    }

    /**
     * @return mixed
     */
    public function getSalary()
    {
        return $this->salary;
    }

    /**
     * @param mixed $salary
     */
    public function setSalary($salary)
    {
        $this->salary = $salary;
    }

    /**
     * Set preferredGender
     *
     * @param string $preferredGender
     *
     * @return Job
     */
    public function setPreferredGender($preferredGender)
    {
        $this->preferredGender = $preferredGender;

        return $this;
    }

    /**
     * Get preferredGender
     *
     * @return string
     */
    public function getPreferredGender()
    {
        return $this->preferredGender;
    }

    /**
     * Set minimumAge
     *
     * @param integer $minimumAge
     *
     * @return Job
     */
    public function setMinimumAge($minimumAge)
    {
        $this->minimumAge = $minimumAge;

        return $this;
    }

    /**
     * Get minimumAge
     *
     * @return int
     */
    public function getMinimumAge()
    {
        return $this->minimumAge;
    }

    /**
     * Set maximumAge
     *
     * @param integer $maximumAge
     *
     * @return Job
     */
    public function setMaximumAge($maximumAge)
    {
        $this->maximumAge = $maximumAge;

        return $this;
    }

    /**
     * Get maximumAge
     *
     * @return int
     */
    public function getMaximumAge()
    {
        return $this->maximumAge;
    }

    /**
     * Set specificRequirement
     *
     * @param string $specificRequirement
     *
     * @return Job
     */
    public function setSpecificRequirement($specificRequirement)
    {
        $this->specificRequirement = $specificRequirement;

        return $this;
    }

    /**
     * Get specificRequirement
     *
     * @return string
     */
    public function getSpecificRequirement()
    {
        return $this->specificRequirement;
    }

    /**
     * Set specificInstruction
     *
     * @param string $specificInstruction
     *
     * @return Job
     */
    public function setSpecificInstruction($specificInstruction)
    {
        $this->specificInstruction = $specificInstruction;

        return $this;
    }

    /**
     * Get specificInstruction
     *
     * @return string
     */
    public function getSpecificInstruction()
    {
        return $this->specificInstruction;
    }

    /**
     * @return mixed
     */
    public function getOnlineLink()
    {
        return $this->onlineLink;
    }

    /**
     * @param mixed $onlineLink
     */
    public function setOnlineLink($onlineLink)
    {
        $this->onlineLink = $onlineLink;
    }

    /**
     * @return mixed
     */
    public function getPostalAddress()
    {
        return $this->postalAddress;
    }

    /**
     * @param mixed $postalAddress
     */
    public function setPostalAddress($postalAddress)
    {
        $this->postalAddress = $postalAddress;
    }

    /**
     * @return JobSetting
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param JobSetting $settings
     *
     * @return Job
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return Job
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function deActivate()
    {
        return $this->status = JobConstants::JOB_STATUS_DELETED;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getJobsFrom()
    {
        return $this->jobsFrom;
    }

    /**
     * @param string $jobsFrom
     *
     * @return Job
     */
    public function setJobsFrom($jobsFrom)
    {
        $this->jobsFrom = $jobsFrom;

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
     * @return Job
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return Job
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return self
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Called before entity removal
     *
     * @ORM\PreRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    /**
     * Called after entity persistence
     *
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }
        $filename = sha1(uniqid(mt_rand(), true));
        $this->path = $filename . '.' . $this->getFile()->getClientOriginalExtension();
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->path
        );

        $srcBase = $this->getUploadRootDir() . '/' . $this->path;
        $dstBase = $this->getUploadThumbnailRootDir() . '/' . $this->path;
//        $resizer->resizeImage($srcBase, $dstBase);

        $this->updatedAt = new \DateTime('now');

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }


    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir() . '/' . $this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }


    protected function getUploadThumbnailRootDir()
    {
        // the absolute directory path where uploaded
        // documents thumbnail should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadThumbnailDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/jobsfile';
    }

    protected function getUploadThumbnailDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/jobsfile/_thumb';
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return Job
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set updatedDate
     *
     * @param \DateTime $updatedDate
     *
     * @return Job
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;

        return $this;
    }

    /**
     * Get updatedDate
     *
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    /**
     * @return mixed
     */
    public function getViewCount()
    {
        return $this->viewCount;
    }

    /**
     * @param mixed $viewCount
     */
    public function setViewCount($viewCount)
    {
        $this->viewCount = $viewCount;
    }

    /**
     * @return Job
     */
    public function increaseViewCount()
    {
        $this->viewCount++;

        return $this;
    }

    /**
     * @return String
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param String $template
     *
     * @return Job
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }


}

