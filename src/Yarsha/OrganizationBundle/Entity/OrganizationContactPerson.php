<?php

namespace Yarsha\OrganizationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * OrganizationContactPerson
 *
 * @ORM\Table(name="ys_organization_contact_persons")
 * @ORM\Entity(repositoryClass="Yarsha\OrganizationBundle\Repository\OrganizationContactPersonRepository")
 * @UniqueEntity("email")
 */
class OrganizationContactPerson
{

    const CONTACT_TYPE_OTHERS = 'others';

    const CONTACT_TYPE_HEAD = 'head';

    const CONTACT_TYPE_MAIN_CONTACT = 'contact';

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
     * @ORM\Column(name="salutation", type="string", length=20, nullable=true)
     */
    private $salutation;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=50, nullable=true)
     */
    private $designation;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=20, nullable=true)
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted = false;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_type", type="string", length=20, nullable=false)
     */
    private $contactType = self::CONTACT_TYPE_OTHERS;

    /**
     * @var Organization
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\OrganizationBundle\Entity\Organization", inversedBy="contactPersons")
     */
    private $organization;


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
     * Set salutation
     *
     * @param string $salutation
     *
     * @return OrganizationContactPerson
     */
    public function setSalutation($salutation)
    {
        $this->salutation = $salutation;

        return $this;
    }

    /**
     * Get salutation
     *
     * @return string
     */
    public function getSalutation()
    {
        return $this->salutation;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return OrganizationContactPerson
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return OrganizationContactPerson
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    public function getName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * Set designation
     *
     * @param string $designation
     *
     * @return OrganizationContactPerson
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get designation
     *
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return OrganizationContactPerson
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     *
     * @return OrganizationContactPerson
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return OrganizationContactPerson
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return OrganizationContactPerson
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

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return OrganizationContactPerson
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
     * Set contactType
     *
     * @param string $contactType
     *
     * @return OrganizationContactPerson
     */
    public function setContactType($contactType)
    {
        $this->contactType = $contactType;

        return $this;
    }

    /**
     * Get contactType
     *
     * @return string
     */
    public function getContactType()
    {
        return $this->contactType;
    }

    /**
     * Get organization
     *
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set organization
     *
     * @param Organization $organization
     *
     * @return OrganizationContactPerson
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }


}

