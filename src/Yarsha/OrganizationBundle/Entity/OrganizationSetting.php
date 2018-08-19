<?php

namespace Yarsha\OrganizationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Yarsha\AdminBundle\Entity\Category;
use Doctrine\Common\Collections\ArrayCollection;
use Yarsha\AdminBundle\Entity\Country;
use Yarsha\AdminBundle\Entity\User;
use Yarsha\OrganizationBundle\OrganizationConstants;

/**
 * Class OrganizationSetting
 * @package Yarsha\OrganizationBundle\Entity
 *
 * @ORM\Table(name="ys_organization_settings")
 * @ORM\Entity(repositoryClass="Yarsha\OrganizationBundle\Repository\OrganizationSettingRepository")
 */
class OrganizationSetting
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
     * @var Organization
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\OrganizationBundle\Entity\Organization")
     */
    private $organization;

    /**
     * @var bool
     *
     * @ORM\Column(name="show_name", type="boolean", nullable=true)
     */
    private $showName = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="show_address", type="boolean", nullable=true)
     */
    private $showAddress = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="show_logo", type="boolean", nullable=true)
     */
    private $showLogo = true;

    /**
     * @var string
     *
     * @ORM\Column(name="auto_email_responder", type="text", nullable=true)
     */
    private $autoEmailResponder;

    /**
     * @var bool
     *
     * @ORM\Column(name="auto_email_status", type="boolean")
     */
    private $autoEmailStatus = false;

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
     * @return OrganizationSetting
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param Organization $organization
     *
     * @return OrganizationSetting
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isShowName()
    {
        return $this->showName;
    }

    /**
     * @param boolean $showName
     *
     * @return OrganizationSetting
     */
    public function setShowName($showName)
    {
        $this->showName = $showName;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isShowAddress()
    {
        return $this->showAddress;
    }

    /**
     * @param boolean $showAddress
     *
     * @return OrganizationSetting
     */
    public function setShowAddress($showAddress)
    {
        $this->showAddress = $showAddress;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isShowLogo()
    {
        return $this->showLogo;
    }

    /**
     * @param boolean $showLogo
     *
     * @return OrganizationSetting
     */
    public function setShowLogo($showLogo)
    {
        $this->showLogo = $showLogo;

        return $this;
    }

    /**
     * @return string
     */
    public function getAutoEmailResponder()
    {
        return $this->autoEmailResponder;
    }

    /**
     * @param string $autoEmailResponder
     *
     * @return OrganizationSetting
     */
    public function setAutoEmailResponder($autoEmailResponder)
    {
        $this->autoEmailResponder = $autoEmailResponder;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAutoEmailStatus()
    {
        return $this->autoEmailStatus;
    }

    /**
     * @param boolean $autoEmailStatus
     *
     * @return OrganizationSetting
     */
    public function setAutoEmailStatus($autoEmailStatus)
    {
        $this->autoEmailStatus = $autoEmailStatus;

        return $this;
    }


}