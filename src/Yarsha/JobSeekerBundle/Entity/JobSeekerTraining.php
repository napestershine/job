<?php

namespace Yarsha\JobSeekerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * JobSeekerTraining
 *
 * @ORM\Table(name="ys_job_seeker_trainings")
 * @ORM\Entity(repositoryClass="Yarsha\JobSeekerBundle\Repository\JobSeekerTrainingRepository")
 */
class JobSeekerTraining
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
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer", nullable=true)
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="institution", type="string", length=255, nullable=true)
     */
    private $institution;

    /**
     * @var string
     *
     * @ORM\Column(name="duration", type="string", length=255, nullable=true)
     */
    private $duration;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="objective", type="text", nullable=true)
     */
    private $objective;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;


    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted = false;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\JobSeekerBundle\Entity\User", inversedBy="trainings", cascade={"persist"})
     */
    private $jobSeeker;


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
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return JobSeekerTraining
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param int $year
     *
     * @return JobSeekerTraining
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return string
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * @param string $institution
     *
     * @return JobSeekerTraining
     */
    public function setInstitution($institution)
    {
        $this->institution = $institution;

        return $this;
    }

    /**
     * @return string
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param string $duration
     *
     * @return JobSeekerTraining
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return JobSeekerTraining
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getObjective()
    {
        return $this->objective;
    }

    /**
     * @param string $objective
     *
     * @return JobSeekerTraining
     */
    public function setObjective($objective)
    {
        $this->objective = $objective;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     *
     * @return JobSeekerTraining
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     *
     * @return JobSeekerTraining
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

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
     * @return JobSeekerTraining
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * @return User
     */
    public function getJobSeeker()
    {
        return $this->jobSeeker;
    }

    /**
     * @param User $jobSeeker
     *
     * @return JobSeekerTraining
     */
    public function setJobSeeker($jobSeeker)
    {
        $this->jobSeeker = $jobSeeker;

        return $this;
    }




}

