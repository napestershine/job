<?php

namespace Yarsha\JobSeekerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Yarsha\JobSeekerBundle\Entity\User as JobSeeker;

/**
 * JobSeekerCallRecord
 *
 * @ORM\Table(name="ys_job_seeker_call_record")
 * @ORM\Entity(repositoryClass="Yarsha\JobSeekerBundle\Repository\JobSeekerCallRecordRepository")
 */
class JobSeekerCallRecord
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
     * @ORM\Column(name="caller_name", type="string", length=20, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="string", length=255, nullable=true)
     */
    private $remark;


    /**
     * @var DateTime
     *
     * @ORM\Column(name="called_date", type="date", length=255, nullable=true)
     */
    private $calledDate;


    /**
     * @var DateTime
     *
     * @ORM\Column(name="follow_up_date", type="date", length=255, nullable=true)
     */
    private $followUpDate;


    /**
     * @var
     * @ORM\OneToOne(targetEntity="Yarsha\JobSeekerBundle\Entity\User")
     */
    private $seeker;


    /**
     * @var int
     *
     * @ORM\Column(name="admin_id", type="integer", nullable=false)
     */
    private $adminId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

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
     * Set remark
     *
     * @param string $remark
     *
     * @return JobSeekerCallRecord
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;

        return $this;
    }

    /**
     * Get remark
     *
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * Set feedback
     *
     * @param string $feedback
     *
     * @return JobSeekerCallRecord
     */
    public function setFeedback($feedback)
    {
        $this->feedback = $feedback;

        return $this;
    }

    /**
     * Get feedback
     *
     * @return string
     */
    public function getFeedback()
    {
        return $this->feedback;
    }


    /**
     * @return mixed
     */
    public function getSeeker()
    {
        return $this->seeker;
    }

    /**
     * @param mixed $seeker
     *
     * @return JobSeekerCallRecord
     */
    public function setSeeker(JobSeeker $seeker)
    {
        $this->seeker = $seeker;

        return $this;
    }

    /**
     * @return int
     */
    public function getAdminId()
    {
        return $this->adminId;
    }

    /**
     * @param int $adminId
     */
    public function setAdminId($adminId)
    {
        $this->adminId = $adminId;
    }

    /**
     * @return DateTime
     */
    public function getCalledDate()
    {
        return $this->calledDate;
    }

    /**
     * @param DateTime $calledDate
     */
    public function setCalledDate($calledDate)
    {
        $this->calledDate = $calledDate;
    }

    /**
     * @return DateTime
     */
    public function getFollowUpDate()
    {
        return $this->followUpDate;
    }

    /**
     * @param DateTime $followUpDate
     */
    public function setFollowUpDate($followUpDate)
    {
        $this->followUpDate = $followUpDate;
    }


    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return JobSeekerCallRecord
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }


}

