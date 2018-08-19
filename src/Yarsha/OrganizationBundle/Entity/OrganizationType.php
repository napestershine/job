<?php

namespace Yarsha\OrganizationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Yarsha\AdminBundle\Entity\User;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * OrganizationType
 *
 * @ORM\Table(name="ys_organization_types")
 * @ORM\Entity(repositoryClass="Yarsha\OrganizationBundle\Repository\OrganizationTypeRepository")
 */
class OrganizationType
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     *
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
     * @ORM\ManyToOne(targetEntity="Yarsha\OrganizationBundle\Entity\OrganizationType", inversedBy="children")
     */
    private $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Yarsha\OrganizationBundle\Entity\OrganizationType", mappedBy="parent")
     */
    private $children;

    /**
     * @var int
     *
     * @ORM\Column(name="position_left", type="integer", nullable=true)
     */
    private $positionLeft = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="position_right", type="integer", nullable=true)
     */
    private $positionRight = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=true)
     *
     * @Gedmo\Timestampable(on="create")
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_date", type="datetime", nullable=true)
     *
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedDate;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\AdminBundle\Entity\User")
     */
    private $createdBy;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status = true;


    /**
     * OrganizationType constructor.
     */
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
     * Set name
     *
     * @param string $name
     *
     * @return OrganizationType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return OrganizationType
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
     * Set description
     *
     * @param string $description
     *
     * @return OrganizationType
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
     * Set parent
     *
     * @param OrganizationType $parent
     *
     * @return OrganizationType
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return OrganizationType
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
     * Set children
     *
     * @param OrganizationType $children
     *
     * @return OrganizationType
     */
    public function addChildren($children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param OrganizationType $children
     *
     * @return OrganizationType
     */
    public function removeChildren($children)
    {
        $this->children->removeElement($children);

        return $this;
    }

    /**
     * Reset children
     *
     * @return OrganizationType
     */
    public function resetChildren()
    {
        $this->children = new ArrayCollection();

        return $this;
    }

    /**
     * Set positionLeft
     *
     * @param integer $positionLeft
     *
     * @return OrganizationType
     */
    public function setPositionLeft($positionLeft)
    {
        $this->positionLeft = $positionLeft;

        return $this;
    }

    /**
     * Get positionLeft
     *
     * @return int
     */
    public function getPositionLeft()
    {
        return $this->positionLeft;
    }

    /**
     * Set positionRight
     *
     * @param integer $positionRight
     *
     * @return OrganizationType
     */
    public function setPositionRight($positionRight)
    {
        $this->positionRight = $positionRight;

        return $this;
    }

    /**
     * Get positionRight
     *
     * @return int
     */
    public function getPositionRight()
    {
        return $this->positionRight;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return OrganizationType
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
     * @return OrganizationType
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
     * Set createdBy
     *
     * @param User $createdBy
     *
     * @return OrganizationType
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
     * Set status
     *
     * @param boolean $status
     *
     * @return OrganizationType
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->name;
    }
}

