<?php

namespace Yarsha\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Yarsha\JobSeekerBundle\Entity\User;

/**
 * Class Location
 * @package Yarsha\MainBundle\Entity
 *
 * @ORM\Entity(repositoryClass="Yarsha\MainBundle\Repository\LocationRepository")
 * @ORM\Table(name="ys_locations")
 */
class Location
{

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\MainBundle\Entity\Country", inversedBy="locations")
     */
    private $country;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted = false;


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
     * Set id
     *
     * @param int $id
     *
     * @return Location
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * Set name
     *
     * @param string $name
     *
     * @return Location
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get country
     *
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set country
     *
     * @param Country $country
     *
     * @return Location
     */
    public function setCountry($country)
    {
        $this->country = $country;

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
     * @return Location
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }



    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->getName();
    }


}
