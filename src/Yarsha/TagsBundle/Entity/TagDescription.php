<?php

namespace Yarsha\TagsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TagDescription
 *
 * @ORM\Table(name="ys_tags_description")
 * @ORM\Entity(repositoryClass="Yarsha\TagsBundle\Repository\TagDescriptionRepository")
 */
class TagDescription
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
     * @var Tag
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\TagsBundle\Entity\Tag", inversedBy="details")
     */
    private $tag;

    /**
     * @var int
     *
     * @ORM\Column(name="entity_id", type="integer")
     */
    private $entityId;

    /**
     * @var string
     *
     * @ORM\Column(name="entity_class", type="string", length=255)
     */
    private $entityClass;


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
     * Set tag
     *
     * @param Tag $tag
     *
     * @return TagDescription
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return Tag
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set entityId
     *
     * @param integer $entityId
     *
     * @return TagDescription
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Get entityId
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set entityClass
     *
     * @param string $entityClass
     *
     * @return TagDescription
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * Get entityClass
     *
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }
}

