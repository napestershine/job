<?php

namespace Yarsha\JobsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JobApplied
 *
 * @ORM\Table(name="ys_job_applied")
 * @ORM\Entity(repositoryClass="Yarsha\JobsBundle\Repository\JobAppliedRepository")
 */
class JobApplied
{

    const APPLY_ONLINE = 'online';

    const APPLY_THROUGH_MAIL = 'email';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var $jobSeeker
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\JobSeekerBundle\Entity\User")
     */
    private $jobSeeker;

    /**
     * @var $job
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\JobsBundle\Entity\Job")
     */
    private $job;

    /**
     * @var string
     *
     * @ORM\Column(name="Type", type="string", length=255)
     */
    private $type;

    /**
     * @var bool
     *
     * @ORM\Column(name="Status", type="boolean", nullable=true)
     */
    private $status = true;


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
     * Set type
     *
     * @param string $type
     *
     * @return JobApplied
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return JobApplied
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
     * @return mixed
     */
    public function getJobSeeker()
    {
        return $this->jobSeeker;
    }

    /**
     * @param mixed $jobSeeker
     */
    public function setJobSeeker($jobSeeker)
    {
        $this->jobSeeker = $jobSeeker;
    }

    /**
     * @return mixed
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param mixed $job
     */
    public function setJob($job)
    {
        $this->job = $job;
    }


}

