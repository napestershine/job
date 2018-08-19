<?php

namespace Yarsha\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Advertisement
 * @package Yarsha\AdminBundle\Entity
 *
 * @ORM\Table(name ="ys_advertisements")
 * @ORM\Entity(repositoryClass="Yarsha\AdminBundle\Repository\AdvertisementRepository")
 */
class Advertisement
{
    const ADV_POSITION_HOME_PAGE_HOT_JOB_SECTION = 'HOT';
    const ADV_POSITION_HOME_PAGE_NEWSPAPER_JOB_SECTION = 'NEWSPAPER';
    const ADV_POSITION_HOME_PAGE_RECENT_JOB_SECTION = 'RECENT';
    
    public static $advSections = [
        self::ADV_POSITION_HOME_PAGE_HOT_JOB_SECTION => [
            'label' => 'Hot job section',
            'h' => 110,
            'w' => 420
        ],
        self::ADV_POSITION_HOME_PAGE_NEWSPAPER_JOB_SECTION => [
            'label' => 'Newspaper job section',
            'h' => 75,
            'w' => 300
        ],
        self::ADV_POSITION_HOME_PAGE_RECENT_JOB_SECTION => [
            'label' => 'Recent job section',
            'h' => 55,
            'w' => 275
        ],
    ];

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
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted = false;

    /**
     * @var bool
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status = true;

    /**
     * @var string
     * @ORM\Column(name="section", type="string", length=20, nullable=true)
     */
    private $section;


    /**
     * @var int
     *
     * @ORM\Column(name="order_status", type="integer", length=3, nullable=true)
     */
    private $order;

    public function setId($id){
        $this->id = $id;
        return $this;
    }


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
     * @return Advertisement
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
     * Set File
     *
     * @param UploadedFile $file
     *
     * @return Advertisement
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
     * @return Advertisement
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
     *
     * @return Advertisement
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }

        return $this;
    }

    /**
     * Called after entity persistence
     *
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     *
     * @return Advertisement
     */
    public function upload()
    {

        if (null === $this->getFile()) {
            return $this;
        }
        $filename = sha1(uniqid(mt_rand(), true));
        $this->path = $filename . '.' . $this->getFile()->getClientOriginalExtension();
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->path
        );

        // clean up the file property as you won't need it anymore
        $this->file = null;

        return $this;
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
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    public function getUploadDir()
    {
        return 'uploads/advertisements';
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
     *
     * @return Advertisement
     */
    public function setOrder($order)
    {
        $this->order = $order;

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
     * @return Advertisement
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isStatus()
    {
        return $this->status;
    }
    /**
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * @param boolean $status
     *
     * @return Advertisement
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @param string $section
     *
     * @return Advertisement
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }



}