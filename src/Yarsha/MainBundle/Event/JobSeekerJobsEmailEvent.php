<?php

namespace Yarsha\MainBundle\Event;


use Yarsha\JobsBundle\Entity\Job;
use Symfony\Component\EventDispatcher\Event;
use Yarsha\JobSeekerBundle\Entity\User as JobSeeker;

/**
 * Class JobSeekerJobsEmailEvent
 * @package Yarsha\MainBundle\Event
 */
class JobSeekerJobsEmailEvent extends Event
{
    /**
     * @var JobSeeker
     */
    private $seeker;

    /**
     * @var Job
     */
    private $job;

    /**
     * @var String
     */
    private $remarks;

    /**
     * JobSeekerEmailEvent constructor.
     * @param JobSeeker $seeker
     * @param Job $job
     * @param string $remarks
     */
    public function __construct(JobSeeker $seeker, Job $job, $remarks = "")
    {
        $this->seeker = $seeker;
        $this->job = $job;
        $this->remarks = $remarks;
    }

    /**
     * @return JobSeeker
     */
    public function getSeeker()
    {
        return $this->seeker;
    }

    /**
     * @param JobSeeker $seeker
     */
    public function setSeeker($seeker)
    {
        $this->seeker = $seeker;
    }

    /**
     * @return Job
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param Job $job
     */
    public function setJob($job)
    {
        $this->job = $job;
    }

    /**
     * @return String
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * @param String $remarks
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }




}