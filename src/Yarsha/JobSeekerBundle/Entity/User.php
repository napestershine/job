<?php


namespace Yarsha\JobSeekerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\DecimalType;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\DateTime;
use Yarsha\JobsBundle\Entity\JobLevel;
use Yarsha\MainBundle\Entity\Category;
use Yarsha\MainBundle\Entity\EducationDegree;
use Yarsha\MainBundle\Entity\Location;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use JMS\Serializer\Annotation\Expose;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class User
 * @package Yarsha\JobSeekerBundle\Entity
 *
 * @ORM\Entity(repositoryClass="Yarsha\JobSeekerBundle\Repository\UserRepository")
 * @ORM\Table(name="ys_job_seekers")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("username")
 * @UniqueEntity("contactEmail")
 */
class User extends BaseUser
{

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=20, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="middle_name", type="string", length=20, nullable=true)
     */
    private $middleName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=20, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=20, nullable=true)
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook_id", type="string", length=255, nullable=true)
     */
    private $facebook_id;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true)
     */
    private $facebookAccessToken;

    /**
     * @var string
     *
     * @ORM\Column(name="linkedin_id", type="string", length=255, nullable=true)
     */
    private $linkedin_id;

    /**
     * @ORM\Column(name="google_id", type="string", nullable=true)
     */
    private $google_id;

    /**
     * @var string
     *
     * @ORM\Column(name="linkedin_access_token", type="string", length=255, nullable=true)
     */
    private $linkedinAccessToken;

    /**
     * @ORM\Column(name="google_access_token", type="string", length=255, nullable=true)
     */
    private $googleAccessToken;

    /**
     * @var string
     *
     * @ORM\Column(name="salutation", type="string", length=25, nullable=true)
     */
    private $salutation;

    /**
     * @var string
     *
     * @ORM\Column(name="father_name", type="string", length=255, nullable=true)
     */
    private $fatherName;

    /**
     * @var string
     *
     * @ORM\Column(name="mother_name", type="string", length=255, nullable=true)
     */
    private $motherName;

    /**
     * @var string
     *
     * @ORM\Column(name="current_address", type="string", length=255, nullable=true)
     */
    private $currentAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="permanent_address", type="string", length=255, nullable=true)
     */
    private $permanentAddress;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="dob", type="date", length=255, nullable=true)
     */
    private $dob;

    /**
     * @var string
     *
     * @ORM\Column(name="marital_status", length=50, type="string", nullable=true)
     */
    private $maritalStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="nationality", length=50, type="string", nullable=true)
     */
    private $nationality;

    /**
     * @var string
     *
     * @ORM\Column(name="religion", length=50, type="string", nullable=true)
     */
    private $religion;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", length=50, type="string", nullable=true)
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", length=50, type="string", nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="office_phone", length=50, type="string", nullable=true)
     */
    private $officePhone;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Yarsha\MainBundle\Entity\Location")
     * @ORM\JoinTable(name="ys_seeker_preferred_locations")
     */
    private $preferredLocations;

    /**
     * @var Category
     *
     * @ORM\ManyToMany(targetEntity="Yarsha\MainBundle\Entity\Category")
     * @ORM\JoinTable(name="ys_seeker_preferred_categories")
     */
    private $preferredCategories;

    /**
     * @var Category
     *
     * @ORM\ManyToMany(targetEntity="Yarsha\MainBundle\Entity\Category")
     * @ORM\JoinTable(name="ys_seeker_preferred_industries")
     */
    private $preferredIndustries;

    /**
     * @var JobLevel
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\JobsBundle\Entity\JobLevel")
     */
    private $preferredPosition;

    /**
     * @var string
     *
     * @ORM\Column(name="available_for", type="string", length=100, nullable=true)
     */
    private $availableFor;

    /**
     * @var DecimalType
     *
     * @ORM\Column(name="expected_salary", type="decimal", nullable=true)
     */
    private $expectedSalary;

    /**
     * @var DecimalType
     *
     * @ORM\Column(name="minimum_expected_salary", type="decimal", nullable=true)
     */
    private $minExpectedSalary;

    /**
     * @var DecimalType
     *
     * @ORM\Column(name="maximum_expected_salary", type="decimal", nullable=true)
     */
    private $maxExpectedSalary;

    /**
     * @var DecimalType
     *
     * @ORM\Column(name="present_salary", type="decimal", nullable=true)
     */
    private $presentSalary;

    /**
     * @var bool
     *
     * @ORM\Column(name="has_experience", type="boolean", nullable=true)
     */
    private $hasExperience;

    /**
     * @var int
     *
     * @ORM\Column(name="no_of_year", type="integer", length=100, nullable=true)
     */
    private $noOfYear;

    /**
     * @var int
     *
     * @ORM\Column(name="no_of_month", type="integer", length=100, nullable=true)
     */
    private $noOfMonth;


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
     * @var
     * @ORM\Column(type="text", length=255, nullable=true)
     */
    protected $curriculumVitaePath;

    /**
     * Image file
     *
     * @var File
     * @Vich\UploadableField(mapping="curriculumVitaeFile", fileNameProperty="curriculumVitaePath")
     *
     * @Assert\File(
     *     maxSize = "1024k",
     *     mimeTypes = {"application/*"},
     *     mimeTypesMessage = "Only the filetypes pdf and word document are allowed."
     * )
     */
    protected $curriculumVitaeFile;

    /**
     * @var bool
     *
     * @ORM\Column(name="profile_completed", nullable=true, type="boolean")
     */
    private $profileCompleted = false;

    /**
     * @var DecimalType
     *
     * @ORM\Column(name="profile_completed_percentage", type="decimal", nullable=true)
     */
    private $profileCompletedPercentage;

    /**
     * @var ProfileCompletion
     *
     * @ORM\OneToOne(targetEntity="Yarsha\JobSeekerBundle\Entity\ProfileCompletion", inversedBy="seeker", orphanRemoval=true)
     */
    private $profileStatus;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Yarsha\JobSeekerBundle\Entity\JobSeekerEducation", orphanRemoval=true, mappedBy="jobSeeker", cascade={"persist", "remove"})
     */
    private $educations;

    /**
     * @var string
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     * @ORM\Column(name="contact_email", type="string", length=255, nullable=true, unique=true)
     */
    private $contactEmail;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Yarsha\JobSeekerBundle\Entity\JobSeekerReference", orphanRemoval=true, mappedBy="jobSeeker", cascade={"persist","remove"})
     */
    private $references;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Yarsha\JobSeekerBundle\Entity\JobSeekerExperience", orphanRemoval=true, mappedBy="jobSeeker", cascade={"persist","remove"})
     */
    private $experiences;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Yarsha\JobSeekerBundle\Entity\JobSeekerTraining", orphanRemoval=true, mappedBy="jobSeeker", cascade={"persist","remove"})
     */
    private $trainings;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Yarsha\JobSeekerBundle\Entity\JobSeekerLanguage", orphanRemoval=true, mappedBy="jobSeeker", cascade={"persist", "remove"})
     */
    private $languages;

    /**
     * @var EducationDegree
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\MainBundle\Entity\EducationDegree")
     */
    private $degree;

    /**
     * @var
     * @ORM\Column(name="profile_visits", type="bigint", nullable=false)
     */
    private $profileVisits = 0;

    /**
     * @var
     * @ORM\Column(name="last_profile_update", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $lastProfileUpdate;

    /**
     * @var
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_blacklisted", type="boolean", nullable=true)
     */
    private $blacklisted = false;

    /**
     * @var string
     *
     * @ORM\Column(name="career_objectives", type="text", nullable=true)
     */
    private $objectives;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_searchable", type="boolean", nullable=true)
     */
    private $isSearchable = false;




    public function __construct()
    {
        parent::__construct();

        $this->addRole('ROLE_JOB_SEEKER');

        $this->preferredLocations = new ArrayCollection();
        $this->preferredCategories = new ArrayCollection();
        $this->preferredIndustries = new ArrayCollection();
        $this->educations = new ArrayCollection();
    }


    public function addPreferredLocation($preferred_location)
    {
        $this->preferredLocations[] = $preferred_location;
    }

    public function removePreferredLocation($preferred_location)
    {
        $this->preferredLocations->removeElement($preferred_location);
    }

    public function addPreferredCategory($preferred_category)
    {
        $this->preferredCategories[] = $preferred_category;
    }

    public function removePreferredCategory($preferred_category)
    {
        $this->preferredCategories->removeElement($preferred_category);
    }

    public function addPreferredIndustry($preferred_industry)
    {
        $this->preferredIndustries[] = $preferred_industry;
    }

    public function removePreferredIndustry($preferred_industry)
    {
        $this->preferredIndustries->removeElement($preferred_industry);
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
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * @param string $facebookId
     *
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebook_id = $facebookId;

        return $this;
    }

    /**
     * @return string
     */
    public function getFacebookAccessToken()
    {
        return $this->facebookAccessToken;
    }

    /**
     * @param string $facebookAccessToken
     *
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebookAccessToken = $facebookAccessToken;

        return $this;
    }


    /**
     * @return string
     */
    public function getGoogleId()
    {
        return $this->google_id;
    }

    /**
     * @param string $googleId
     *
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->google_id = $googleId;

        return $this;
    }


    /**
     * @return string
     */
    public function getGoogleAccessToken()
    {
        return $this->googleAccessToken;
    }

    /**
     * @param string $googleAccessToken
     *
     * @return User
     */
    public function setGoogleAccessToken($googleAccessToken)
    {
        $this->googleAccessToken = $googleAccessToken;

        return $this;
    }


    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     *
     * @return User
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalutation()
    {
        return $this->salutation;
    }

    /**
     * @param string $salutation
     *
     * @return User
     */
    public function setSalutation($salutation)
    {
        $this->salutation = $salutation;

        return $this;
    }

    /**
     * @return string
     */
    public function getFatherName()
    {
        return $this->fatherName;
    }

    /**
     * @param string $father_name
     *
     * @return User
     */
    public function setFatherName($father_name)
    {
        $this->fatherName = $father_name;

        return $this;
    }

    /**
     * @return string
     */
    public function getMotherName()
    {
        return $this->motherName;
    }

    /**
     * @param string $mother_name
     *
     * @return User
     */
    public function setMotherName($mother_name)
    {
        $this->motherName = $mother_name;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentAddress()
    {
        return $this->currentAddress;
    }

    /**
     * @param string $current_address
     *
     * @return User
     */
    public function setCurrentAddress($current_address)
    {
        $this->currentAddress = $current_address;

        return $this;
    }

    /**
     * @return string
     */
    public function getPermanentAddress()
    {
        return $this->permanentAddress;
    }

    /**
     * @param string $permanent_address
     *
     * @return User
     */
    public function setPermanentAddress($permanent_address)
    {
        $this->permanentAddress = $permanent_address;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * @param DateTime $dob
     *
     * @return User
     */
    public function setDob($dob)
    {
        $this->dob = $dob;

        return $this;
    }

    /**
     * @return string
     */
    public function getMaritalStatus()
    {
        return $this->maritalStatus;
    }

    /**
     * @param string $marital_status
     *
     * @return User
     */
    public function setMaritalStatus($marital_status)
    {
        $this->maritalStatus = $marital_status;

        return $this;
    }

    /**
     * @return string
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * @param string $nationality
     *
     * @return User
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * @return string
     */
    public function getReligion()
    {
        return $this->religion;
    }

    /**
     * @param string $religion
     *
     * @return User
     */
    public function setReligion($religion)
    {
        $this->religion = $religion;

        return $this;
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     *
     * @return User
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getOfficePhone()
    {
        return $this->officePhone;
    }

    /**
     * @param string $office_phone
     *
     * @return User
     */
    public function setOfficePhone($office_phone)
    {
        $this->officePhone = $office_phone;

        return $this;
    }

    /**
     * @return Location
     */
    public function getPreferredLocations()
    {
        return $this->preferredLocations;
    }

    public function getPreferredLocationsIdArray()
    {
        $preferredLocations = $this->preferredLocations;
        $result = [];
        foreach ($preferredLocations as $location) {
            $result[] = $location->getId();
        }

        return $result;
    }

    /**
     * @return Category
     */
    public function getPreferredCategories()
    {
        return $this->preferredCategories;
    }

    public function getPreferredCategoriesIdArray()
    {
        $preferredCategories = $this->preferredCategories;
        $result = [];
        foreach ($preferredCategories as $category) {
            $result[] = $category->getId();
        }

        return $result;
    }


    /**
     * @return Category
     */
    public function getPreferredIndustries()
    {
        return $this->preferredIndustries;
    }

    public function getPreferredIndustriesIdArray()
    {
        $preferredIndustries = $this->preferredIndustries;
        $result = [];
        foreach ($preferredIndustries as $industry) {
            $result[] = $industry->getId();
        }

        return $result;
    }

    /**
     * @return JobLevel
     */
    public function getPreferredPosition()
    {
        return $this->preferredPosition;
    }

    /**
     * @param JobLevel $preferred_position
     *
     * @return User
     */
    public function setPreferredPosition($preferred_position)
    {
        $this->preferredPosition = $preferred_position;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvailableFor()
    {
        return $this->availableFor;
    }

    /**
     * @param string $available_for
     *
     * @return User
     */
    public function setAvailableFor($available_for)
    {
        $this->availableFor = $available_for;

        return $this;
    }

    /**
     * @return DecimalType
     */
    public function getExpectedSalary()
    {
        return $this->expectedSalary;
    }

    /**
     * @param DecimalType $expected_salary
     *
     * @return User
     */
    public function setExpectedSalary($expected_salary)
    {
        $this->expectedSalary = $expected_salary;

        return $this;
    }


    /**
     * @return DecimalType
     */
    public function getMinExpectedSalary()
    {
        return $this->minExpectedSalary;
    }

    /**
     * @param DecimalType $minExpectedSalary
     *
     * @return User
     */
    public function setMinExpectedSalary($minExpectedSalary)
    {
        $this->minExpectedSalary = $minExpectedSalary;

        return $this;
    }


    /**
     * @return DecimalType
     */
    public function getMaxExpectedSalary()
    {
        return $this->maxExpectedSalary;
    }

    /**
     * @param DecimalType $maxExpectedSalary
     *
     * @return User
     */
    public function setMaxExpectedSalary($maxExpectedSalary)
    {
        $this->maxExpectedSalary = $maxExpectedSalary;

        return $this;
    }

    /**
     * @return DecimalType
     */
    public function getPresentSalary()
    {
        return $this->presentSalary;
    }

    /**
     * @param DecimalType $present_salary
     *
     * @return User
     */
    public function setPresentSalary($present_salary)
    {
        $this->presentSalary = $present_salary;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isHasExperience()
    {
        return $this->hasExperience;
    }

    /**
     * @param boolean $hasExperience
     *
     * @return User
     */
    public function setHasExperience($hasExperience)
    {
        $this->hasExperience = $hasExperience;

        return $this;
    }


    /**
     * @return int
     */
    public function getNoOfYear()
    {
        return $this->noOfYear;
    }

    /**
     * @param int $no_of_year
     *
     * @return User
     */
    public function setNoOfYear($no_of_year)
    {
        $this->noOfYear = $no_of_year;

        return $this;
    }

    /**
     * @return int
     */
    public function getNoOfMonth()
    {
        return $this->noOfMonth;
    }

    /**
     * @param int $no_of_month
     *
     * @return User
     */
    public function setNoOfMonth($no_of_month)
    {
        $this->noOfMonth = $no_of_month;

        return $this;
    }

//    /**
//     * @return string
//     */
//    public function getPhoto()
//    {
//        return $this->photo;
//    }
//
//    /**
//     * @param string $photo
//     */
//    public function setPhoto($photo)
//    {
//        $this->photo = $photo;
//    }

    /**
     * @return boolean
     */
    public function isProfileCompleted()
    {
        return $this->profileCompleted;
    }

    /**
     * @param boolean $profile_completed
     *
     * @return User
     */
    public function setProfileCompleted($profile_completed)
    {
        $this->profileCompleted = $profile_completed;

        return $this;
    }

    /**
     * @return DecimalType
     */
    public function getProfileCompletedPercentage()
    {
        return $this->profileCompletedPercentage;
    }

    /**
     * @param DecimalType $profile_completed_percentage
     *
     * @return User
     */
    public function setProfileCompletedPercentage($profile_completed_percentage)
    {
        $this->profileCompletedPercentage = $profile_completed_percentage;

        return $this;
    }


    /**
     * Sets file.
     *
     * @param UploadedFile $file
     *
     * @return User
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

        return $this;

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
        $file = $file = $this->getAbsolutePath() . $this->path;
        if (file_exists($file) && is_file($file)) {
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

    public function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    public function getUploadPath()
    {
        return __DIR__ . '/../../../../web/';
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
        return 'uploads/seekers';
    }

    protected function getUploadThumbnailDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/seekers/_thumb';
    }

    /**
     * @return string
     */
    public function getLinkedinId()
    {
        return $this->linkedin_id;
    }

    /**
     * @param string $linkedin_id
     */
    public function setLinkedinId($linkedin_id)
    {
        $this->linkedin_id = $linkedin_id;
    }

    /**
     * @return string
     */
    public function getLinkedinAccessToken()
    {
        return $this->linkedinAccessToken;
    }

    /**
     * @param string $linkedinAccessToken
     */
    public function setLinkedinAccessToken($linkedinAccessToken)
    {
        $this->linkedinAccessToken = $linkedinAccessToken;
    }

    /**
     * @return ProfileCompletion
     */
    public function getProfileStatus()
    {
        return $this->profileStatus;
    }

    /**
     * @param ProfileCompletion $profileStatus
     *
     * @return User
     */
    public function setProfileStatus($profileStatus)
    {
        $this->profileStatus = $profileStatus;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getEducations()
    {
        return $this->educations;
    }

    public function setEducations($educations){
        $this->educations = $educations;
        foreach ($educations as $education){
            $education->setJobSeeker($this);
        }
    }

    /**
     * Add education
     *
     * @param JobSeekerEducation $education
     *
     * @return User
     */
    public function addEducation(JobSeekerEducation $education)
    {
        $this->educations->add($education);
        $education->setJobSeeker($this);
        return $this;
    }

    /**
     * Remove education
     *
     * @param JobSeekerEducation $education
     *
     * @return User
     */
    public function removeEducation(JobSeekerEducation $education)
    {
        if($this->educations->contains($education)){
            $this->educations->removeElement($education);
        }
        return $this;
    }

    /**
     * Reset educations
     *
     * @return User
     */
    public function resetEducation()
    {
        $this->educations = new ArrayCollection();
        return $this;
    }

    /**
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * @param string $contactEmail
     *
     * @return User
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getReferences()
    {
        return $this->references;
    }

    public function setReferences($references){
        $this->references = $references;
        foreach ($references as $reference){
            $reference->setJobSeeker($this);
        }
    }

    /**
     * Add reference
     *
     * @param JobSeekerReference $reference
     *
     * @return User
     */
    public function addReferences($reference)
    {
        $this->references[] = $reference;
        return $this;
    }

    /**
     * Add reference
     *
     * @param JobSeekerReference $reference
     *
     * @return User
     */
    public function addReference($reference)
    {
        $this->references[] = $reference;
        return $this;
    }

    /**
     * @param JobSeekerReference $reference
     */
    public function removeReference(JobSeekerReference $reference){
        if($this->references->contains($reference)){
            $this->references->removeElement($reference);
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getExperiences()
    {
        return $this->experiences;
    }

    public function setExperiences($experiences){
        $this->experiences = $experiences;
        foreach ($experiences as $experience){
            $experience->setJobSeeker($this);
        }
    }

    /**
     * Add experience
     *
     * @param JobSeekerExperience $experience
     *
     * @return User
     */
    public function addExperience($experience)
    {
        $this->experiences[] = $experience;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getTrainings()
    {
        return $this->trainings;
    }

    public function setTrainings($trainings){
        $this->trainings = $trainings;
        foreach ($trainings as $training){
            $training->setJobSeeker($this);
        }
    }

    /**
     * Add training
     *
     * @param JobSeekerTraining $training
     *
     * @return User
     */
    public function addTraining(JobSeekerTraining $training)
    {
        $this->trainings[] = $training;
        $training->setJobSeeker($this);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @param $languages
     */
    public function setLanguages($languages){
        $this->languages = $languages;
        foreach ($languages as $language){
            $language->setJobSeeker($this);
        }
    }

    /**
     * Add language
     *
     * @param JobSeekerLanguage $language
     *
     * @return User
     */
    public function addLanguage(JobSeekerLanguage $language)
    {
        $this->languages[] = $language;

        return $this;
    }

    /**
     * @param JobSeekerLanguage $language
     */
    public function removeLanguage(JobSeekerLanguage $language){
        if($this->languages->contains($language)){
            $this->languages->removeElement($language);
        }
    }


    /**
     * Get hasExperience
     *
     * @return boolean
     */
    public function getHasExperience()
    {
        return $this->hasExperience;
    }

    /**
     * @param UploadedFile $curriculumVitaeFile
     */
    public function setCurriculumVitaeFile(UploadedFile $curriculumVitaeFile)
    {
        $this->curriculumVitaeFile = $curriculumVitaeFile;
    }

    public function getCurriculumVitaeFile()
    {
        return $this->curriculumVitaeFile;
    }

    /**
     * Set curriculumVitaePath
     *
     * @param string $curriculumVitaePath
     *
     * @return User
     */
    public function setCurriculumVitaePath($curriculumVitaePath)
    {
        $this->curriculumVitaePath = $curriculumVitaePath;

        return $this;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function uploadCv()
    {
        if (null === $this->getCurriculumVitaeFile()) {
            return;
        }

        $filename = 'CV_' . str_replace(' ', '_', $this->getFullName());
        $this->curriculumVitaePath = $filename . '.' . $this->getCurriculumVitaeFile()->getClientOriginalExtension();
        $this->getCurriculumVitaeFile()->move(
            $this->getUploadRootDir(),
            $this->curriculumVitaePath
        );

        $this->curriculumVitaeFile = null;
    }

    public function getAbsoluteCurriculumVitaePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->curriculumVitaePath;
    }


//    /**
//     * @param ExecutionContextInterface $context
//     * @Assert\Callback
//     */
//    public function validate(ExecutionContextInterface $context)
//    {
//        if (!in_array($this->curriculumVitaeFile->getMimeType(), [
//            'application/pdf',
//            'application/x-pdf',
//            'application/msword'
//        ])
//        ) {
//            $context
//                ->buildViolation('Wrong file type (pdf and ms-word accepted)')
//                ->atPath('curriculumVitaeFile')
//                ->addViolation();
//        }
//    }

    /**
     * Get curriculumVitaePath
     *
     * @return string
     */
    public function getCurriculumVitaePath()
    {
        return $this->curriculumVitaePath;
    }

    /**
     * Get profileCompleted
     *
     * @return boolean
     */
    public function getProfileCompleted()
    {
        return $this->profileCompleted;
    }

    /**
     *
     * @return mixed
     */
    public function getProfileVisits()
    {
        return $this->profileVisits;
    }

    /**
     * @param mixed $profileVisits
     *
     * @return User
     */
    public function setProfileVisits($profileVisits)
    {
        $this->profileVisits = $profileVisits;

        return $this;
    }

    /**
     * @return User
     */
    public function increaseProfileVisits()
    {
        $this->profileVisits++;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastProfileUpdate()
    {
        return $this->lastProfileUpdate;
    }

    /**
     * @param mixed $lastProfileUpdateDate
     *
     * @return User
     */
    public function setLastProfileUpdate($lastProfileUpdateDate)
    {
        $this->lastProfileUpdate = $lastProfileUpdateDate;

        return $this;
    }

    /**
     * @return mixed
     * @return User
     */
    public function getCreatedAt()
    {
        return $this->createdAt;

        return $this;
    }

    /**
     * @param mixed $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return EducationDegree
     */
    public function getDegree()
    {
        return $this->degree;
    }

    /**
     * @param EducationDegree $degree
     *
     * @return User
     */
    public function setDegree($degree)
    {
        $this->degree = $degree;

        return $this;
    }

    public function __toString()
    {
        return $this->firstName ?: '';
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $deleted
     *
     * @return User
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function deActivate()
    {
        return $this->deleted = true;
    }

    /**
     * @return boolean
     */
    public function isBlacklisted()
    {
        return $this->blacklisted;
    }

    /**
     * @param boolean $blacklisted
     *
     * @return User
     */
    public function setBlacklisted($blacklisted)
    {
        $this->blacklisted = $blacklisted;

        return $this;
    }

    public function getFullName()
    {
        $name = $this->getFirstName();
        $name = $this->getMiddleName() ? $name . " " . $this->getMiddleName() : $name;
        $name = $this->getLastName() ? $name . " " . $this->getLastName() : $name;

        return $name;
    }

    /**
     * @return string
     */
    public function getObjectives()
    {
        return $this->objectives;
    }

    /**
     * @param string $objectives
     */
    public function setObjectives($objectives)
    {
        $this->objectives = $objectives;
    }

    /**
     * @return bool
     */
    public function isSearchable()
    {
        return $this->isSearchable;
    }

    /**
     * @param bool $isSearchable
     */
    public function setIsSearchable($isSearchable)
    {
        $this->isSearchable = $isSearchable;
    }




}
