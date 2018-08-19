<?php

namespace Yarsha\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Yarsha\AdminBundle\Entity\User;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Category
 *
 * @ORM\Table(name="ys_categories")
 *
 * @ORM\Entity(repositoryClass="Yarsha\MainBundle\Repository\CategoryRepository")
 */
class Category
{

    const CATEGORY_TYPE_JOB_BY_FUNCTION = "Jobs By Function";

    const CATEGORY_TYPE_JOB_BY_INDUSTRY = "Jobs By Industry";


    public static $category_types = [
        self::CATEGORY_TYPE_JOB_BY_FUNCTION => "Jobs By Function",
        self::CATEGORY_TYPE_JOB_BY_INDUSTRY => "Jobs By Industry"
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
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     *
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="section", type="string", length=255, nullable=true)
     */
    private $section;

    /**
     * @var bool
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted = false;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="integer")
     */
    private $level = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer")
     */
    private $sortOrder = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     */
    private $createdDate;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\AdminBundle\Entity\User")
     */
    private $createdBy;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\MainBundle\Entity\Category", inversedBy="children")
     */
    private $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Yarsha\MainBundle\Entity\Category", mappedBy="parent")
     */
    private $children;


    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

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
     * @return Category
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
     * @return Category
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
     * Set section
     *
     * @param string $section
     *
     * @return Category
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get section
     *
     * @return string
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return Category
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return bool
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Category
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return bool
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set level
     *
     * @param integer $level
     *
     * @return Category
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set sortOrder
     *
     * @param integer $sortOrder
     *
     * @return Category
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
     * @return Category
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
     * Set createdBy
     *
     * @param User $createdBy
     *
     * @return Category
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set parent
     *
     * @param Category $parent
     *
     * @return Category
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get children
     *
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add children
     *
     * @param Category $children
     *
     * @return Category
     */
    public function addChildren($children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param Category $children
     *
     * @return Category
     */
    public function removeChildren($children)
    {
        $this->children->removeElement($children);

        return $this;
    }

    /**
     * Reset children
     *
     * @return Category
     */
    public function resetChildren()
    {
        $this->children = new ArrayCollection();

        return $this;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->title;
    }

}

