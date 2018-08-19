<?php

namespace Yarsha\MainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Country
 * @package Yarsha\MainBundle\Entity
 *
 * @ORM\Entity(repositoryClass="Yarsha\MainBundle\Repository\CountryRepository")
 * @ORM\Table(name="ys_countries")
 */
class Country
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
     * @var string
     *
     * @ORM\Column(name="iso2", type="string", length=2, nullable=true)
     */
    private $iso2;

    /**
     * @var string
     *
     * @ORM\Column(name="iso3", type="string", length=3, nullable=true)
     */
    private $iso3;

    /**
     * @var string
     *
     * @ORM\Column(name="zip_code", type="string", length=20, nullable=true)
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="nationality", type="string", length=50, nullable=true)
     */
    private $nationality;

    /**
     * @var Location
     * @ORM\OneToMany(targetEntity="Yarsha\MainBundle\Entity\Country", mappedBy="country", cascade={"persist"})
     */
    private $locations;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted = false;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
    }

    public function addLocation($location)
    {
        $this->locations[] = $location;
    }

    public function getLocations()
    {
        return $this->locations;
    }

    public function removeLocation($location)
    {
        $this->locations->removeElement($location);
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
     * Set id
     *
     * @param int $id
     *
     * @return Country
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
     * @return Country
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get iso2
     *
     * @return string
     */
    public function getIso2()
    {
        return $this->iso2;
    }

    /**
     * Set iso2
     *
     * @param string $iso2
     *
     * @return Country
     */
    public function setIso2($iso2)
    {
        $this->iso2 = $iso2;

        return $this;
    }

    /**
     * Get iso3
     *
     * @return string
     */
    public function getIso3()
    {
        return $this->iso3;
    }

    /**
     * Set iso3
     *
     * @param string $iso3
     *
     * @return Country
     */
    public function setIso3($iso3)
    {
        $this->iso3 = $iso3;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set zipCode
     *
     * @param string $zipCode
     *
     * @return Country
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get nationality
     *
     * @return string
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * Set nationality
     *
     * @param string $nationality
     *
     * @return Country
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;

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
     * @return Country
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }



    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->name;
    }


}
