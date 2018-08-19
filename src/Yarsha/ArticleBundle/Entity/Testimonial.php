<?php

namespace Yarsha\ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Yarsha\ArticleBundle\ArticleConstants;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use JMS\Serializer\Annotation\Expose;


/**
 * Testimonial
 *
 * @ORM\Table(name="ys_testimonials")
 * @ORM\Entity(repositoryClass="Yarsha\ArticleBundle\Repository\TestimonialRepository")
 */
class Testimonial implements ArticleInterface
{

    const TESTIMONIAL_STATUS_PUBLISHED = 1;

    const TESTIMONIAL_STATUS_DRAFT = 2;

    const TESTIMONIAL_STATUS_DELETED = 3;

    const TESTIMONIAL_TYPE_TESTIMONIAL = 1;



    public static $testimonialStatusOptions = [
        self::TESTIMONIAL_STATUS_PUBLISHED => 'Published',
        self::TESTIMONIAL_STATUS_DRAFT => 'Draft'
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
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="company", type="string", length=255, nullable=true)
     */
    private $company;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", length=5)
     */
    private $status = ArticleConstants::ARTICLE_STATUS_DRAFT;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    private $isDeleted = false;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=true)
     */
    private $sortOrder = 0;

//    /**
//     * @var string
//     *
//     * @ORM\Column(name="image", type="string", length=255, nullable=true)
//     */
//    private $image;

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
     *     maxSize = "1M",
     *     mimeTypes = {"image/*"},
     *     maxSizeMessage = "The maximum allowed file size is 1MB.",
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
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_type", type="integer", length=5, nullable=true)
     */
    private $userType;

    public function setId($id){
        $this->id = $id;
        return $this;
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
     * @return Testimonial
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
     * Set company
     *
     * @param string $company
     *
     * @return Testimonial
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Testimonial
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Testimonial
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     *
     * @return Testimonial
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return bool
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set sortOrder
     *
     * @param integer $sortOrder
     *
     * @return Testimonial
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }



    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return Testimonial
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
     * @return Testimonial
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
     * Set userId
     *
     * @param integer $id
     *
     * @return Testimonial
     */
    public function setUserId($id)
    {
        $this->userId = $id;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set userType
     *
     * @param int $userType
     *
     * @return Testimonial
     */
    public function setUserType($userType)
    {
        $this->userType = $userType;

        return $this;
    }

    /**
     * Get userType
     *
     * @return int
     */
    public function getUserType()
    {
        return $this->userType;
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
        return 'uploads/testimonials';
    }

    protected function getUploadThumbnailDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/testimonials/_thumb';
    }
}

