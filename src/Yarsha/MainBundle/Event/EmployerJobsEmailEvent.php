<?php

namespace Yarsha\MainBundle\Event;


use Yarsha\JobsBundle\Entity\Job;
use Symfony\Component\EventDispatcher\Event;
use Yarsha\EmployerBundle\Entity\User as Employer;

/**
 * Class EmployerJobsEmailEvent
 * @package Yarsha\MainBundle\Event
 */
class EmployerJobsEmailEvent extends Event
{
    /**
     * @var Employer
     */
    private $employer;

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
     * @param Employer $employer
     * @param Job $job
     * @param string $remarks
     */
    public function __construct(Employer $employer, Job $job, $remarks = "")
    {
        $this->employer = $employer;
        $this->job = $job;
        $this->remarks = $remarks;
    }

    /**
     * @return Employer $employer
     */
    public function getEmployer()
    {
        return $this->employer;
    }

    /**
     * @param $employer
     */
    public function setEmployer($employer)
    {
        $this->employer = $employer;
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