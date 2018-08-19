<?php

namespace Yarsha\JobSeekerBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * EmployeeAppliedJob
 *
 * @ORM\Table(name="ys_job_seeker_applied_jobs")
 * @ORM\Entity(repositoryClass="Yarsha\JobSeekerBundle\Repository\EmployeeAppliedJobRepository")
 */
class EmployeeAppliedJob
{

    const JOB_APPLIED_TYPE_ONLINE = 'online';

    const JOB_APPLIED_TYPE_EMAIL = 'email';

    const JOB_APPLIED_STATUS_PENDING = 'pending';

    const JOB_APPLIED_STATUS_SELECTED = 'selected';

    const JOB_APPLIED_STATUS_REJECTED = 'rejected';

    const JOB_APPLIED_STATUS_SAVE = 'save';

    const JOB_APPLIED_STATUS_SHORTLISTED = 'shortlisted';


    public static $appliedJobStatus = [

        self::JOB_APPLIED_STATUS_PENDING => 'PENDING',
        self::JOB_APPLIED_STATUS_SELECTED => 'SELECTED',
        self::JOB_APPLIED_STATUS_REJECTED => 'REJECTED',
        self::JOB_APPLIED_STATUS_SAVE => 'SAVE',
        self::JOB_APPLIED_STATUS_SHORTLISTED => 'SHORTLISTED'
    ];


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
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\JobSeekerBundle\Entity\User")
     */
    private $employee;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status = self::JOB_APPLIED_STATUS_PENDING;

    /**
     * @var string
     *
     * @ORM\Column(name="createdDate", type="datetime", length=255)
     * @Gedmo\Timestampable(on="create")
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
     * @return EmployeeAppliedJob
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
     * @return EmployeeAppliedJob
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
     * @param string $createdDate
     *
     * @return EmployeeAppliedJob
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return string
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return EmployeeAppliedJob
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
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


}

