<?php

namespace Yarsha\OrganizationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Expose;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Yarsha\MainBundle\Entity\Country;
use Gedmo\Mapping\Annotation as Gedmo;
use Yarsha\MainBundle\Entity\Category;
use Symfony\Component\HttpFoundation\File\File;
use Yarsha\AdminBundle\Entity\User as AdminUser;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Yarsha\OrganizationBundle\OrganizationConstants;
use Yarsha\JobSeekerBundle\Entity\User as JobSeeker;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Organization
 * @package Yarsha\OrganizationBundle\Entity
 *
 * @ORM\Table(name="ys_organizations")
 * @ORM\Entity(repositoryClass="Yarsha\OrganizationBundle\Repository\OrganizationRepository")
 * @UniqueEntity("email")
 */
class Organization
{

    const NORMAL_EMPLOYER = 'normal';

    const FEATURED_EMPLOYER = 'featured';

    const TOP_EMPLOYER = 'top';

    const SUPER_EMPLOYER = 'super';

    const HIRING_EMPLOYER = 'hiring';


    public static $orgCategory = [
        'NORMAL EMPLOYER' => 'normal',
        'FEATURED EMPLOYER' => 'featured',
        'TOP EMPLOYER' => 'top',
        'SUPER EMPLOYER' => 'super',
        'HIRING EMPLOYER' => 'hiring',
    ];

    public static $organizationCategoryTypes = [
        self::NORMAL_EMPLOYER => 'Normal',
        self::FEATURED_EMPLOYER => 'Featured',
        self::TOP_EMPLOYER => 'Top',
        self::SUPER_EMPLOYER => 'Super',
        self::HIRING_EMPLOYER => 'Hiring',
    ];


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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;


    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var OrganizationType
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\OrganizationBundle\Entity\OrganizationType")
     */
    private $type;

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
     * @var OrganizationOwnership
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\OrganizationBundle\Entity\OrganizationOwnership")
     */
    private $ownershipType;

    /**
     * @var OrganizationSize
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\OrganizationBundle\Entity\OrganizationSize")
     */
    private $size;

//    /**
//     * @var string
//     *
//     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
//     */
//    private $logo;

    /**
     * @var string
     *
     * @ORM\Column(name="profile", type="text", nullable=true)
     */
    private $profile;

    /**
     * @var OrganizationContactPerson
     *
     * @ORM\OneToMany(targetEntity="Yarsha\OrganizationBundle\Entity\OrganizationContactPerson", mappedBy="organization")
     */
    private $contactPersons;

    /**
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\MainBundle\Entity\Country")
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=50, nullable=true)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=50, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\Column(name="post_box", type="string", length=50, nullable=true)
     */
    private $postBox;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="secondary_email", type="string", length=255, nullable=true)
     */
    private $secondaryEmail;

    /**
     * @var int
     *
     * @ORM\Column(name="profile_status", type="integer", length=3, nullable=true)
     */
    private $profileStatus = OrganizationConstants::ORGANIZATION_PROFILE_STATUS_ACTIVE;

    /**
     * @var
     * @ORM\Column(name="profile_completed_percentage", type="integer", length=3, nullable=true)
     */
    private $profileCompletedPercentage;


    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", length=3, nullable=true)
     */
    private $status = OrganizationConstants::ORGANIZATION_STATUS_PENDING;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdDate;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=true)
     */
    private $sortOrder = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="access_code", type="string", length=100, nullable=true)
     */
    private $accessCode;

    /**
     * @var string
     *
     * @ORM\Column(name="external_link", type="string", length=255, nullable=true)
     */
    private $externalLink;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_date", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedDate;

    /**
     * @var AdminUser
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\AdminBundle\Entity\User")
     */
    private $approvedBy;

    /**
     * @var int
     *
     * @ORM\Column(name="visit", type="integer", length=11, nullable=true)
     */
    private $visit = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="nature", type="string", length=255, nullable=true)
     */
    private $nature;

    /**
     * @var int
     *
     * @ORM\Column(name="label", type="integer", length=3, nullable=true)
     */
    private $label;

    /**
     * @var OrganizationSetting
     *
     * @ORM\OneToOne(targetEntity="Yarsha\OrganizationBundle\Entity\OrganizationSetting", mappedBy="organization")
     */
    private $settings;

    /**
     * @var string
     * @ORM\Column(name="category_type", length=255, type="string", nullable=true)
     */
    private $categoryType;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_newspaper_organization", type="boolean", nullable=true)
     */
    private $isNewspaperOrganization = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_goverment_organization", type="boolean", nullable=true)
     */
    private $isGovermentOrganization = false;

    /**
     * @var AdminUser
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\AdminBundle\Entity\User")
     */
    private $accountManager;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted = false;

    /**
     * @ORM\ManyToMany(targetEntity="Yarsha\JobSeekerBundle\Entity\User")
     * @ORM\JoinTable(name="ys_organization_followers",
     *      joinColumns={@ORM\JoinColumn(name="organization_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="follower_id", referencedColumnName="id")}
     * )
     */
    private $followers;


    /**
     * Organization constructor.
     */
    public function __construct()
    {
        $this->contactPersons = new ArrayCollection();
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
     * @return Organization
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Organization
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Organization
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Organization
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return OrganizationType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param OrganizationType $type
     *
     * @return Organization
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     *
     * @return Organization
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Category
     */
    public function getIndustry()
    {
        return $this->industry;
    }

    /**
     * @param Category $category
     *
     * @return Organization
     */
    public function setIndustry($category)
    {
        $this->industry = $category;

        return $this;
    }

    /**
     * @return OrganizationOwnership
     */
    public function getOwnershipType()
    {
        return $this->ownershipType;
    }

    /**
     * @param OrganizationOwnership $ownershipType
     *
     * @return Organization
     */
    public function setOwnershipType($ownershipType)
    {
        $this->ownershipType = $ownershipType;

        return $this;
    }

    /**
     * @return OrganizationSize
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param OrganizationSize $size
     *
     * @return Organization
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

//    /**
//     * @return string
//     */
//    public function getLogo()
//    {
//        return $this->logo;
//    }

//    /**
//     * @param string $logo
//     *
//     * @return Organization
//     */
//    public function setLogo($logo)
//    {
//        $this->logo = $logo;
//
//        return $this;
//    }

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
     *     maxSize = "2M",
     *     mimeTypes = {"image/*"},
     *     maxSizeMessage = "The maximum allowed file size is 2MB.",
     *     mimeTypesMessage = "Only Images are allowed."
     * )
     */
    protected $file;


    /**
     * Image path
     *
     * @var string
     *
     * @ORM\Column(type="text", length=255, nullable=true)
     *
     * @Expose
     */
    protected $coverpath;

    /**
     * Image file
     *
     * @var File
     * @Vich\UploadableField(mapping="", fileNameProperty="path")
     *
     * @Assert\File(
     *     maxSize = "2M",
     *     mimeTypes = {"image/*"},
     *     maxSizeMessage = "The maximum allowed file size is 2MB.",
     *     mimeTypesMessage = "Only Images are allowed."
     * )
     */
    protected $coverfile;


    /**
     * @return string
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param string $profile
     *
     * @return Organization
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return OrganizationContactPerson
     */
    public function getContactPersons()
    {
        return $this->contactPersons;
    }

    /**
     * @param OrganizationContactPerson $contactPerson
     *
     * @return Organization
     */
    public function addContactPerson($contactPerson)
    {
        $this->contactPersons[] = $contactPerson;

        return $this;
    }

    /**
     * @param OrganizationContactPerson $contactPerson
     *
     * @return Organization
     */
    public function removeContactPerson($contactPerson)
    {
        $this->contactPersons->removeElement($contactPerson);

        return $this;
    }

    /**
     * @return Organization
     */
    public function resetContactPersons()
    {
        $this->contactPersons = new ArrayCollection();

        return $this;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Country $country
     *
     * @return Organization
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return Organization
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     *
     * @return Organization
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

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
     * @return Organization
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     *
     * @return Organization
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * @return string
     */
    public function getPostBox()
    {
        return $this->postBox;
    }

    /**
     * @param string $postBox
     *
     * @return Organization
     */
    public function setPostBox($postBox)
    {
        $this->postBox = $postBox;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return Organization
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecondaryEmail()
    {
        return $this->secondaryEmail;
    }

    /**
     * @param string $secondaryEmail
     *
     * @return Organization
     */
    public function setSecondaryEmail($secondaryEmail)
    {
        $this->secondaryEmail = $secondaryEmail;

        return $this;
    }

    /**
     * @return int
     */
    public function getProfileStatus()
    {
        return $this->profileStatus;
    }

    /**
     * @param int $profileStatus
     *
     * @return Organization
     */
    public function setProfileStatus($profileStatus)
    {
        $this->profileStatus = $profileStatus;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProfileCompletedPercentage()
    {
        return $this->profileCompletedPercentage;
    }

    /**
     * @param mixed $profileCompletedPercentage
     */
    public function setProfileCompletedPercentage($profileCompletedPercentage)
    {
        $this->profileCompletedPercentage = $profileCompletedPercentage;
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
     * @return Organization
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
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
     *
     * @return Organization
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @param int $sortOrder
     *
     * @return Organization
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccessCode()
    {
        return $this->accessCode;
    }

    /**
     * @param string $accessCode
     *
     * @return Organization
     */
    public function setAccessCode($accessCode)
    {
        $this->accessCode = $accessCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getExternalLink()
    {
        return $this->externalLink;
    }

    /**
     * @param string $externalLink
     *
     * @return Organization
     */
    public function setExternalLink($externalLink)
    {
        $this->externalLink = $externalLink;

        return $this;
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
     *
     * @return Organization
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;

        return $this;
    }

    /**
     * @return AdminUser
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * @param AdminUser $approvedBy
     *
     * @return Organization
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;

        return $this;
    }

    /**
     * @return int
     */
    public function getVisit()
    {
        return $this->visit;
    }

    /**
     * @param int $visit
     *
     * @return Organization
     */
    public function setVisit($visit)
    {
        $this->visit = $visit;

        return $this;
    }

    public function increaseVisitCount()
    {
        $this->visit++;
    }


    /**
     * @return string
     */
    public function getNature()
    {
        return $this->nature;
    }

    /**
     * @param string $nature
     *
     * @return Organization
     */
    public function setNature($nature)
    {
        $this->nature = $nature;

        return $this;
    }

    /**
     * @return int
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param int $label
     *
     * @return Organization
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return OrganizationSetting
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param OrganizationSetting $settings
     *
     * @return Organization
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
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
        return 'uploads/employers';
    }

    protected function getUploadThumbnailDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/employers/_thumb';
    }

    /**
     * @return string
     */
    public function getCategoryType()
    {
        return $this->categoryType;
    }

    /**
     * @param string $categoryType
     *
     * @return Organization
     */
    public function setCategoryType($categoryType)
    {
        $this->categoryType = $categoryType;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isIsNewspaperOrganization()
    {
        return $this->isNewspaperOrganization;
    }

    /**
     * @param boolean $isNewspaperOrganization
     *
     * @return Organization
     */
    public function setIsNewspaperOrganization($isNewspaperOrganization)
    {
        $this->isNewspaperOrganization = $isNewspaperOrganization;

        return $this;
    }


    /**
     * @return boolean
     */
    public function isIsGovermentOrganization()
    {
        return $this->isGovermentOrganization;
    }

    /**
     * @param boolean $isGovermentOrganization
     *
     * @return Organization
     */
    public function setIsGovermentOrganization($isGovermentOrganization)
    {
        $this->isGovermentOrganization = $isGovermentOrganization;

        return $this;
    }

    /**
     * Sets coverfile.
     *
     * @param UploadedFile $coverfile
     */
    public function setCoverFile(UploadedFile $coverfile = null)
    {
        $this->coverfile = $coverfile;

    }

    /**
     * Get coverfile.
     *
     * @return UploadedFile
     */
    public function getCoverFile()
    {
        return $this->coverfile;
    }

    /**
     * @return string
     */
    public function getCoverPath()
    {
        return $this->coverpath;
    }

    /**
     * @param string $coverpath
     *
     * @return self
     */
    public function setCoverPath($coverpath)
    {
        $this->coverpath = $coverpath;

        return $this;
    }


    /**
     * Called after entity persistence
     *
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function uploadCover()
    {
        if (null === $this->getCoverFile()) {
            return;
        }
        $filename = sha1(uniqid(mt_rand(), true));
        $this->path = $filename . '.' . $this->getCoverFile()->getClientOriginalExtension();
        $this->getCoverFile()->move(
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

    /**
     * @return AdminUser
     */
    public function getAccountManager()
    {
        return $this->accountManager;
    }

    /**
     * @param AdminUser $accountManager
     *
     * @return Organization
     */
    public function setAccountManager($accountManager)
    {
        $this->accountManager = $accountManager;

        return $this;
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
     * @return Organization
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

    public function isFollowedBy(JobSeeker $user)
    {
        return $this->followers->contains($user);
    }

}
