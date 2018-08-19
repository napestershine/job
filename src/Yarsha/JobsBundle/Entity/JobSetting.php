<?php

namespace Yarsha\JobsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JobSetting
 *
 * @ORM\Table(name="ys_job_setting")
 * @ORM\Entity(repositoryClass="Yarsha\JobsBundle\Repository\JobSettingRepository")
 */
class JobSetting
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
     * @var Job
     *
     * @ORM\OneToOne(targetEntity="Yarsha\JobsBundle\Entity\Job", inversedBy="settings")
     */
    private $job;

    /**
     * @var bool
     *
     * @ORM\Column(name="applyOnline", type="boolean")
     */
    private $applyOnline;

    /**
     * @var bool
     *
     * @ORM\Column(name="applyEmail", type="boolean")
     */
    private $applyEmail;

    /**
     * @var bool
     *
     * @ORM\Column(name="applyPost", type="boolean")
     */
    private $applyPost;

    /**
     * @var bool
     *
     * @ORM\Column(name="uploadDocument", type="boolean", nullable=true)
     */
    private $uploadDocument = false;


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
     * @param Job $job
     *
     * @return JobSetting
     */
    public function setJob($job)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * Get job
     *
     * @return Job
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * Set applyOnline
     *
     * @param boolean $applyOnline
     *
     * @return JobSetting
     */
    public function setApplyOnline($applyOnline)
    {
        $this->applyOnline = $applyOnline;

        return $this;
    }

    /**
     * Get applyOnline
     *
     * @return bool
     */
    public function getApplyOnline()
    {
        return $this->applyOnline;
    }

    /**
     * Set applyEmail
     *
     * @param boolean $applyEmail
     *
     * @return JobSetting
     */
    public function setApplyEmail($applyEmail)
    {
        $this->applyEmail = $applyEmail;

        return $this;
    }

    /**
     * Get applyEmail
     *
     * @return bool
     */
    public function getApplyEmail()
    {
        return $this->applyEmail;
    }

    /**
     * Set applyPost
     *
     * @param boolean $applyPost
     *
     * @return JobSetting
     */
    public function setApplyPost($applyPost)
    {
        $this->applyPost = $applyPost;

        return $this;
    }

    /**
     * Get applyPost
     *
     * @return bool
     */
    public function getApplyPost()
    {
        return $this->applyPost;
    }

    /**
     * @return boolean
     */
    public function isUploadDocument()
    {
        return $this->uploadDocument;
    }

    /**
     * @param boolean $uploadDocument
     */
    public function setUploadDocument($uploadDocument)
    {
        $this->uploadDocument = $uploadDocument;
    }


}

