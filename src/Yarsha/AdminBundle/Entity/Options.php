<?php

namespace Yarsha\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Options
 * @package Yarsha\AdminBundle\Entity
 *
 * @ORM\Table(name="ys_options")
 * @ORM\Entity(repositoryClass="Yarsha\AdminBundle\Repository\OptionsRepository")
 */
class Options
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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    private $value;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status = true;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     *
     * @return Options
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return Options
     */
    public function setValue($value)
    {
        $this->value = $value;

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
     * @param boolean $status
     *
     * @return Options
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }


}