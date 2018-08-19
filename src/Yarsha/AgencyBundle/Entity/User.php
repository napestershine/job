<?php

/*
 * This file is part of the RollerworksMultiUserBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Yarsha\AgencyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * Class User
 * @package Yarsha\AgencyBundle\Entity
 *
 * @ORM\Entity(repositoryClass="Yarsha\AgencyBundle\Repository\AgencyUserRepository")
 * @ORM\Table(name="ys_agencies")
 */

class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     */
    private $phone;


    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;


    /**
     * @var string
     *
     * @ORM\Column(name="contact_person_name", type="string", length=255, nullable=true)
     */
    private $contactPersonName;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_person_email", type="string", length=255, nullable=true)
     */
    private $contactPersonEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_person_phone", type="string", length=255, nullable=true)
     */
    private $contactPersonPhone;

    /**
     * @var string
     * @ORM\Column(name="access_token", type="string", length=255, nullable=true)
     */
    private $accessToken;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted = false;

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getContactPersonName()
    {
        return $this->contactPersonName;
    }

    /**
     * @param string $contactPersonName
     */
    public function setContactPersonName($contactPersonName)
    {
        $this->contactPersonName = $contactPersonName;
    }

    /**
     * @return string
     */
    public function getContactPersonEmail()
    {
        return $this->contactPersonEmail;
    }

    /**
     * @param string $contactPersonEmail
     */
    public function setContactPersonEmail($contactPersonEmail)
    {
        $this->contactPersonEmail = $contactPersonEmail;
    }

    /**
     * @return string
     */
    public function getContactPersonPhone()
    {
        return $this->contactPersonPhone;
    }

    /**
     * @param string $contactPersonPhone
     */
    public function setContactPersonPhone($contactPersonPhone)
    {
        $this->contactPersonPhone = $contactPersonPhone;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }



}
