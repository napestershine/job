<?php

namespace Yarsha\JobSeekerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JobSeekerSetting
 *
 * @ORM\Table(name="ys_job_seeker_settings")
 * @ORM\Entity(repositoryClass="Yarsha\JobSeekerBundle\Repository\JobSeekerSettingRepository")
 */
class JobSeekerSetting
{

    const VEHICLE_TYPE_NONE = 'none';

    const VEHICLE_TYPE_TW = 'two wheeler';

    const VEHICLE_TYPE_FW = 'four wheeler';

    const VEHICLE_TYPE_BOTH = 'both';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="travel_for_job", type="boolean")
     */
    private $travelForJob = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="have_license", type="boolean")
     */
    private $haveLicense = false;

    /**
     * @var string
     *
     * @ORM\Column(name="have_license_of", type="string", length=255)
     */
    private $haveLicenseOf = JobSeekerSetting::VEHICLE_TYPE_NONE;

    /**
     * @var bool
     *
     * @ORM\Column(name="willing_to_relocation", type="boolean")
     */
    private $willingToRelocation = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="have_vehicle", type="boolean")
     */
    private $haveVehicle = false;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicle_type", type="string")
     */
    private $vehicleType = JobSeekerSetting::VEHICLE_TYPE_NONE;

    /**
     * @var bool
     *
     * @ORM\Column(name="profile_searchable", type="boolean")
     */
    private $profile_searchable = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="profile_confidential", type="boolean")
     */
    private $profile_confidential = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="job_alert_table", type="boolean")
     */
    private $job_alert_table = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="facebook_alert", type="boolean")
     */
    private $facebook_alert = false;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="Yarsha\JobSeekerBundle\Entity\User")
     * @ORM\JoinColumn(name="job_seeker_id")
     */
    private $jobSeeker;

    public function setId($id){
        $this->id = $id;
        return $this;
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

    /**
     * Set travelForJob
     *
     * @param boolean $travelForJob
     *
     * @return JobSeekerSetting
     */
    public function setTravelForJob($travelForJob)
    {
        $this->travelForJob = $travelForJob;

        return $this;
    }

    /**
     * Get travelForJob
     *
     * @return bool
     */
    public function getTravelForJob()
    {
        return $this->travelForJob;
    }

    /**
     * Set haveLicense
     *
     * @param boolean $haveLicense
     *
     * @return JobSeekerSetting
     */
    public function setHaveLicense($haveLicense)
    {
        $this->haveLicense = $haveLicense;

        return $this;
    }

    /**
     * Get haveLicense
     *
     * @return bool
     */
    public function getHaveLicense()
    {
        return $this->haveLicense;
    }

    /**
     * Set haveLicenseOf
     *
     * @param string $haveLicenseOf
     *
     * @return JobSeekerSetting
     */
    public function setHaveLicenseOf($haveLicenseOf)
    {
        $this->haveLicenseOf = $haveLicenseOf;

        return $this;
    }

    /**
     * Get haveLicenseOf
     *
     * @return string
     */
    public function getHaveLicenseOf()
    {
        return $this->haveLicenseOf;
    }

    /**
     * Set willingToRelocation
     *
     * @param boolean $willingToRelocation
     *
     * @return JobSeekerSetting
     */
    public function setWillingToRelocation($willingToRelocation)
    {
        $this->willingToRelocation = $willingToRelocation;

        return $this;
    }

    /**
     * Get willingToRelocation
     *
     * @return bool
     */
    public function getWillingToRelocation()
    {
        return $this->willingToRelocation;
    }

    /**
     * @return boolean
     */
    public function isHaveVehicle()
    {
        return $this->haveVehicle;
    }

    /**
     * @param boolean $haveVehicle
     *
     * @return JobSeekerSetting
     */
    public function setHaveVehicle($haveVehicle)
    {
        $this->haveVehicle = $haveVehicle;

        return $this;
    }

    /**
     * @return string
     */
    public function getVehicleType()
    {
        return $this->vehicleType;
    }

    /**
     * @param string $vehicleType
     *
     * @return JobSeekerSetting
     */
    public function setVehicleType($vehicleType)
    {
        $this->vehicleType = $vehicleType;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isProfileSearchable()
    {
        return $this->profile_searchable;
    }

    /**
     * @param boolean $profile_searchable
     *
     * @return JobSeekerSetting
     */
    public function setProfileSearchable($profile_searchable)
    {
        $this->profile_searchable = $profile_searchable;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isProfileConfidential()
    {
        return $this->profile_confidential;
    }

    /**
     * @param boolean $profile_confidential
     *
     * @return JobSeekerSetting
     */
    public function setProfileConfidential($profile_confidential)
    {
        $this->profile_confidential = $profile_confidential;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isJobAlertTable()
    {
        return $this->job_alert_table;
    }

    /**
     * @param boolean $job_alert_table
     *
     * @return JobSeekerSetting
     */
    public function setJobAlertTable($job_alert_table)
    {
        $this->job_alert_table = $job_alert_table;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isFacebookAlert()
    {
        return $this->facebook_alert;
    }

    /**
     * @param boolean $facebook_alert
     *
     * @return JobSeekerSetting
     */
    public function setFacebookAlert($facebook_alert)
    {
        $this->facebook_alert = $facebook_alert;

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
     * @return JobSeekerSetting
     */
    public function setJobSeeker($jobSeeker)
    {
        $this->jobSeeker = $jobSeeker;

        return $this;
    }


}

