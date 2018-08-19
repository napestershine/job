<?php

namespace Yarsha\JobSeekerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JobSeekerLanguage
 *
 * @ORM\Table(name="ys_job_seeker_languages")
 * @ORM\Entity(repositoryClass="Yarsha\JobSeekerBundle\Repository\JobSeekerLanguageRepository")
 */
class JobSeekerLanguage
{

    const Excellent = 'excellent';

    const GOOD = 'good';

    const AVERAGE = 'average';

    const POOR = 'poor';

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
     * @ORM\Column(name="language", type="string", length=255)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="reading", type="string", length=255)
     */
    private $reading;

    /**
     * @var string
     *
     * @ORM\Column(name="writing", type="string", length=255)
     */
    private $writing;

    /**
     * @var string
     *
     * @ORM\Column(name="speaking", type="string", length=255)
     */
    private $speaking;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\JobSeekerBundle\Entity\User", inversedBy="languages", cascade={"persist"})
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
     * Set language
     *
     * @param string $language
     *
     * @return JobSeekerLanguage
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set reading
     *
     * @param string $reading
     *
     * @return JobSeekerLanguage
     */
    public function setReading($reading)
    {
        $this->reading = $reading;

        return $this;
    }

    /**
     * Get reading
     *
     * @return string
     */
    public function getReading()
    {
        return $this->reading;
    }

    /**
     * Set writing
     *
     * @param string $writing
     *
     * @return JobSeekerLanguage
     */
    public function setWriting($writing)
    {
        $this->writing = $writing;

        return $this;
    }

    /**
     * Get writing
     *
     * @return string
     */
    public function getWriting()
    {
        return $this->writing;
    }

    /**
     * Set speaking
     *
     * @param string $speaking
     *
     * @return JobSeekerLanguage
     */
    public function setSpeaking($speaking)
    {
        $this->speaking = $speaking;

        return $this;
    }

    /**
     * Get speaking
     *
     * @return string
     */
    public function getSpeaking()
    {
        return $this->speaking;
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
     * @return  JobSeekerLanguage
     */
    public function setJobSeeker($jobSeeker)
    {
        $this->jobSeeker = $jobSeeker;

        return $this;
    }

}

