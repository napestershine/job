<?php

namespace Yarsha\JobSeekerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * JobSeekerEducation
 *
 * @ORM\Table(name="ys_job_seeker_educations")
 * @ORM\Entity(repositoryClass="Yarsha\JobSeekerBundle\Repository\JobSeekerEducationRepository")
 */
class JobSeekerEducation
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
     * @ORM\ManyToOne(targetEntity="Yarsha\MainBundle\Entity\EducationDegree")
     */
    private $degree;

    /**
     * @var
     * @ORM\Column(name="degree_name", type="text", nullable=true)
     */
    private $degreeName = '';

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
     * @ORM\Column(name="board", type="string", length=255, nullable=true)
     */
    private $board;

    /**
     * @var string
     *
     * @ORM\Column(name="mark_system", type="string", length=255, nullable=true)
     */
    private $markSystem;

    /**
     * @var string
     *
     * @ORM\Column(name="percentage", type="string", length=255, nullable=true)
     */
    private $percentage;

    /**
     * @var string
     *
     * @ORM\Column(name="specification", type="string", length=255, nullable=true)
     */
    private $specification;

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
    private $deleted = 0;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\MainBundle\Entity\Country")
     */
    private $country;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Yarsha\JobSeekerBundle\Entity\User", inversedBy="educations", cascade={"persist"})
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
     * Set degree
     *
     * @param string $degree
     *
     * @return JobSeekerEducation
     */
    public function setDegree($degree)
    {
        $this->degree = $degree;

        return $this;
    }

    /**
     * Get degree
     *
     * @return string
     */
    public function getDegree()
    {
        return $this->degree;
    }

    /**
     * @return mixed
     */
    public function getDegreeName()
    {
        return $this->degreeName;
    }

    /**
     * @param mixed $degreeName
     *
     * @return JobSeekerEducation
     */
    public function setDegreeName($degreeName)
    {
        $this->degreeName = $degreeName;

        return $this;
    }

    /**
     * Set year
     *
     * @param integer $year
     *
     * @return JobSeekerEducation
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set institution
     *
     * @param string $institution
     *
     * @return JobSeekerEducation
     */
    public function setInstitution($institution)
    {
        $this->institution = $institution;

        return $this;
    }

    /**
     * Get institution
     *
     * @return string
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Set board
     *
     * @param string $board
     *
     * @return JobSeekerEducation
     */
    public function setBoard($board)
    {
        $this->board = $board;

        return $this;
    }

    /**
     * Get board
     *
     * @return string
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * Set markSystem
     *
     * @param string $markSystem
     *
     * @return JobSeekerEducation
     */
    public function setMarkSystem($markSystem)
    {
        $this->markSystem = $markSystem;

        return $this;
    }

    /**
     * Get markSystem
     *
     * @return string
     */
    public function getMarkSystem()
    {
        return $this->markSystem;
    }

    /**
     * Set percentage
     *
     * @param string $percentage
     *
     * @return JobSeekerEducation
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;

        return $this;
    }

    /**
     * Get percentage
     *
     * @return string
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * Set specification
     *
     * @param string $specification
     *
     * @return JobSeekerEducation
     */
    public function setSpecification($specification)
    {
        $this->specification = $specification;

        return $this;
    }

    /**
     * Get specification
     *
     * @return string
     */
    public function getSpecification()
    {
        return $this->specification;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return JobSeekerEducation
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

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return JobSeekerEducation
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return bool
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     *
     * @return JobSeekerEducation
     */
    public function setCountry($country)
    {
        $this->country = $country;

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
     * @return JobSeekerEducation
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

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
     * @return  JobSeekerEducation
     */
    public function setJobSeeker(User $jobSeeker)
    {
        $this->jobSeeker = $jobSeeker;

        return $this;
    }


}

