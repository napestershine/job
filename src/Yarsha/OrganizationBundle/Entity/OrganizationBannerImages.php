<?php

namespace Yarsha\OrganizationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use JMS\Serializer\Annotation\Expose;

/**
 * Class OrganizationBannerImages
 * @package Yarsha\OrganizationBundle\Entity
 *
 * @ORM\Table(name ="ys_organizayions_banner_images")
 * @ORM\Entity(repositoryClass="Yarsha\OrganizationBundle\Repository\OrganizationBannerImagesRepository")
 */
class OrganizationBannerImages
{


    const IMAGE_STATUS_ACTIVE = 1;

    const IMAGE_STATUS_INACTIVE = 2;


    /**
     * @var int
     * @ORM\Column(name="id",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var string
     * @ORM\Column(name="caption",type="string", length=255, nullable=true)
     *
     */
    private $caption;

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
     *     maxSize = "5M",
     *     mimeTypes = {"image/*"},
     *     maxSizeMessage = "The maximum allowed file size is 5MB.",
     *     mimeTypesMessage = "Only the filetypes image are allowed.",
     *
     * )
     */
    protected $file;


    /**
     * @var bool
     *
     * @ORM\Column(name="is_featured", type="boolean", nullable=true)
     */
    private $isFeatured = false;


    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", length=3, nullable=true)
     */
    private $status = self::IMAGE_STATUS_ACTIVE;


    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Yarsha\OrganizationBundle\Entity\Organization")
     */
    private $employer;


    /**
     * @var int
     *
     * @ORM\Column(name="order_status", type="integer", length=3, nullable=true)
     */
    private $order;


    /**
     * @return int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * @param string $caption
     *
     * @return OrganizationBannerImages
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * @return string
     */
    public function getCaption()
    {

        return $this->caption;
    }

    /**
     * @param boolean $isFeatured
     *
     * @return OrganizationBannerImages
     */
    public function setIsFeatured($isFeatured)
    {
        $this->isFeatured = $isFeatured;

        return $this;
    }


    /**
     * @return boolean
     */
    public function getIsFeatured()
    {

        return $this->isFeatured;
    }


    /**
     * Set File
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

    public function getUploadRootDir()
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


    public function getUploadResizeRootDir()
    {
        // the absolute directory path where uploaded
        // documents thumbnail should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadResizeDir();
    }

    public function getUploadDir()
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

    public function getUploadResizeDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/employers/resize';
    }


    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }


    public function deActivate()
    {
        return $this->status = self::IMAGE_STATUS_INACTIVE;
    }

    public function makeFeatured()
    {

        return $this->isFeatured = true;
    }

    public function deactivateFeatured()
    {

        return $this->isFeatured = false;
    }


    /**
     * Set employer
     *
     * @param string $employer
     *
     * @return OrganizationBannerImages
     */
    public function setEmployer($employer)
    {
        $this->employer = $employer;

        return $this;
    }

    /**
     * Get employer
     *
     * @return string
     */
    public function getEmployer()
    {
        return $this->employer;
    }


}
