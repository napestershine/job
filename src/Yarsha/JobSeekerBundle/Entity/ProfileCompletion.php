<?php

namespace Yarsha\JobSeekerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Yarsha\JobSeekerBundle\Entity\User as Seeker;

/**
 * ProfileCompletion
 *
 * @ORM\Table(name="ys_job_seeker_profile_completion")
 * @ORM\Entity(repositoryClass="Yarsha\JobSeekerBundle\Repository\ProfileCompletionRepository")
 */
class ProfileCompletion
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
     * @var Seeker
     *
     * @ORM\OneToOne(targetEntity="Yarsha\JobSeekerBundle\Entity\User", mappedBy="profileStatus")
     */
    private $seeker;

    /**
     * @var float
     *
     * @ORM\Column(name="personal", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $personal;

    /**
     * @var float
     *
     * @ORM\Column(name="general", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $general;

    /**
     * @var float
     *
     * @ORM\Column(name="education", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $education;

    /**
     * @var float
     *
     * @ORM\Column(name="training", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $training;

    /**
     * @var float
     *
     * @ORM\Column(name="professional", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $professional;

    /**
     * @var float
     *
     * @ORM\Column(name="others", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $others;

    /**
     * @var float
     *
     * @ORM\Column(name="overall", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $overall;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_cv_uploaded", type="boolean", nullable=true)
     */
    private $isCVUploaded = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="can_apply", type="boolean", nullable=true)
     */
    private $canApply = false;


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
     * Set seeker
     *
     * @param Seeker $seeker
     *
     * @return ProfileCompletion
     */
    public function setSeeker($seeker)
    {
        $this->seeker = $seeker;

        return $this;
    }

    /**
     * Get seeker
     *
     * @return Seeker
     */
    public function getSeeker()
    {
        return $this->seeker;
    }

    /**
     * Set personal
     *
     * @param float $personal
     *
     * @return ProfileCompletion
     */
    public function setPersonal($personal)
    {
        $this->personal = $personal;

        return $this;
    }

    /**
     * Get personal
     *
     * @return float
     */
    public function getPersonal()
    {
        return $this->personal;
    }

    /**
     * Set general
     *
     * @param float $general
     *
     * @return ProfileCompletion
     */
    public function setGeneral($general)
    {
        $this->general = $general;

        return $this;
    }

    /**
     * Get general
     *
     * @return float
     */
    public function getGeneral()
    {
        return $this->general;
    }

    /**
     * Set education
     *
     * @param float $education
     *
     * @return ProfileCompletion
     */
    public function setEducation($education)
    {
        $this->education = $education;

        return $this;
    }

    /**
     * Get education
     *
     * @return float
     */
    public function getEducation()
    {
        return $this->education;
    }

    /**
     * Set training
     *
     * @param float $training
     *
     * @return ProfileCompletion
     */
    public function setTraining($training)
    {
        $this->training = $training;

        return $this;
    }

    /**
     * Get training
     *
     * @return float
     */
    public function getTraining()
    {
        return $this->training;
    }

    /**
     * Set professional
     *
     * @param float $professional
     *
     * @return ProfileCompletion
     */
    public function setProfessional($professional)
    {
        $this->professional = $professional;

        return $this;
    }

    /**
     * Get professional
     *
     * @return float
     */
    public function getProfessional()
    {
        return $this->professional;
    }

    /**
     * Set others
     *
     * @param float $others
     *
     * @return ProfileCompletion
     */
    public function setOthers($others)
    {
        $this->others = $others;

        return $this;
    }

    /**
     * Get others
     *
     * @return float
     */
    public function getOthers()
    {
        return $this->others;
    }

    /**
     * Set overall
     *
     * @param float $overall
     *
     * @return ProfileCompletion
     */
    public function setOverall($overall)
    {
        $this->overall = $overall;

        return $this;
    }

    /**
     * Get overall
     *
     * @return float
     */
    public function getOverall()
    {
        return $this->overall;
    }

    /**
     * Set isCVUploaded
     *
     * @param boolean $isCVUploaded
     *
     * @return ProfileCompletion
     */
    public function setIsCVUploaded($isCVUploaded)
    {
        $this->isCVUploaded = $isCVUploaded;

        return $this;
    }

    /**
     * Get isCVUploaded
     *
     * @return bool
     */
    public function getIsCVUploaded()
    {
        return $this->isCVUploaded;
    }

    /**
     * Set canApply
     *
     * @param boolean $canApply
     *
     * @return ProfileCompletion
     */
    public function setCanApply($canApply)
    {
        $this->canApply = $canApply;

        return $this;
    }

    /**
     * Get canApply
     *
     * @return bool
     */
    public function getCanApply()
    {
        return $this->canApply;
    }
}

