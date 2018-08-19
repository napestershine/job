<?php

namespace Yarsha\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Banner
 * @package Yarsha\AdminBundle\Entity
 *
 * @ORM\Table(name ="ys_banners")
 * @ORM\Entity(repositoryClass="Yarsha\AdminBundle\Repository\BannerRepository")
 */
class Banner
{

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
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted = false;


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
     * @return Banner
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
     * @return Banner
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
     *
     * @return Banner
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
     * @return Banner
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
     * @return Banner
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
     * @return Banner
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
        return 'uploads/banners';
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
     * @return Banner
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
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
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $deleted
     *
     * @return Banner
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }



}