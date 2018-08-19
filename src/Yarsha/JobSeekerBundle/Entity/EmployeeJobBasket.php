<?php

namespace Yarsha\JobSeekerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * EmployeeJobBasket
 *
 * @ORM\Table(name="ys_job_seeker_jobs_basket")
 * @ORM\Entity(repositoryClass="Yarsha\JobSeekerBundle\Repository\EmployeeJobBasketRepository")
 */
class EmployeeJobBasket
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
     * @ORM\ManyToOne(targetEntity="Yarsha\JobsBundle\Entity\Job")
     */
    private $job;

    /**
     * @var string
     * @ORM\ManyToOne(targetEntity="Yarsha\JobSeekerBundle\Entity\User")
     */
    private $employee;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdDate", type="datetime")
     * @Gedmo\Timestampable(on="create")
     *
     */
    private $createdDate;


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
     * Set job
     *
     * @param string $job
     *
     * @return EmployeeJobBasket
     */
    public function setJob($job)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * Get job
     *
     * @return string
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * Set employee
     *
     * @param string $employee
     *
     * @return EmployeeJobBasket
     */
    public function setEmployee($employee)
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get employee
     *
     * @return string
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return EmployeeJobBasket
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
}

