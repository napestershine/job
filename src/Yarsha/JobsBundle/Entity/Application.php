<?php

namespace Yarsha\JobsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Yarsha\JobSeekerBundle\Entity\User;
use Gedmo\Mapping\Annotation as Gedmo;
use Yarsha\JobsBundle\Entity\Job;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Job
 *
 * @ORM\Table(name="ys_applications")
 * @ORM\Entity(repositoryClass="Yarsha\JobsBundle\Repository\JobRepository")
 */
class Application
{

    const JOBS_APPLICATION_APPLIED = 200;

    const JOBS_APPLICATION_SHORTLIST = 201;

    const JOBS_APPLICATION_REJECTED = 202;

    const JOBS_APPLICATION_SELECTED = 203;


    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\JobSeekerBundle\Entity\User")
     */
    private $jobseeker;

    /**
     * @var Job
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\JobsBundle\Entity\Job")
     */
    private $job;


    /**
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="integer")
     */
    private $status = self::JOBS_APPLICATION_APPLIED;


    public function __construct()
    {
        $this->locations = new ArrayCollection();
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

    public function created()
    {
        return $this->created;
    }


    /**
     * @return mixed
     */
    public function getJobseeker()
    {
        return $this->jobseeker;
    }

    /**
     * @param mixed $jobseeker
     */
    public function setJobseeker($jobseeker)
    {
        $this->jobseeker = $jobseeker;
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

    /**
     * @ORM\return datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @ORM\param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }


}

