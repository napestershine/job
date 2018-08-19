<?php

namespace Yarsha\JobSeekerBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\JobSeekerBundle\JobSeekerConstants;
use Yarsha\MainBundle\Service\AbstractService;
use Yarsha\JobSeekerBundle\Entity\ProfileCompletion;
use Yarsha\JobSeekerBundle\Entity\User as JobSeeker;

/**
 * Class JobSeekerProfileService
 * @package Yarsha\JobSeekerBundle\Service
 *
 * @DI\Service("yarsha.service.jobseeker_profile", parent="yarsha.service.abstract")
 * @DI\Tag("yarsha.abstract_service")
 */
class JobSeekerProfileService extends AbstractService
{

    public static $profileGroupPersonal = [
        'firstName',
        'lastName',
        'gender',
        'nationality',
        'dob',
        'currentAddress',
        'permanentAddress',
        'mobile'
    ];

    public static $profileGroupGeneral = [
        'preferredIndustries',
        'preferredLocations',
        'preferredCategories'
    ];

    public static $profileGroupEducation = ['educations'];

    public static $profileGroupTrainings = ['trainings'];

    public static $profileGroupProfessionals = ['experiences'];

    public static $profileGroupOthers = ['references', 'languages'];

    public static $pgpCompletionStatus;

    public static $pggCompletionStatus;

    public static $educationStatus;

    public static $trainingStatus;

    public static $experienceStatus;

    public static $pgoCompletionStatus;


    public function updateProfileCompletion(JobSeeker $jobSeeker)
    {
        $em = $this->getEntityManager();
        $profile = $jobSeeker->getProfileStatus();

        if (!$profile) {
            $profile = new ProfileCompletion();
            $profile->setSeeker($jobSeeker);
            $jobSeeker->setProfileStatus($profile);
            $em->persist($jobSeeker);
        }

        $this->setProfileCompletionStatus($jobSeeker);

        $profile->setPersonal(self::$pgpCompletionStatus);
        $profile->setGeneral(self::$pggCompletionStatus);
        $profile->setEducation(self::$educationStatus);
        $profile->setTraining(self::$trainingStatus);
        $profile->setProfessional(self::$experienceStatus);
        $profile->setOthers(self::$pgoCompletionStatus);
        $profile->setOverall($this->getProfileCompletionStatus($jobSeeker));
        $isCVUploaded = $jobSeeker->getCurriculumVitaePath() == '' ? false : true;
        $profile->setIsCVUploaded($isCVUploaded);
        $canApply = $this->canApplyForJob($jobSeeker);
        $profile->setCanApply($canApply);
        $jobSeeker->setProfileCompletedPercentage($profile->getOverall());
        $em->persist($jobSeeker);
        $em->persist($profile);
        $em->flush();
    }

    public function setProfileCompletionStatus(JobSeeker $jobSeeker)
    {
        $pgpCount = 0;
        foreach (self::$profileGroupPersonal as $profile) {
            $method = 'get' . ucfirst($profile);
            if ($jobSeeker->$method() != '') {
                $pgpCount++;
            }
        }
        self::$pgpCompletionStatus = ($pgpCount / count(self::$profileGroupPersonal)) * 100;

        $pggCount = 0;
        foreach (self::$profileGroupGeneral as $profile) {
            $method = 'get' . ucfirst($profile);
            if (count($jobSeeker->$method()) > 0) {
                $pggCount++;
            }
        }
        self::$pggCompletionStatus = ($pggCount / count(self::$profileGroupGeneral)) * 100;

        self::$trainingStatus = 0;
        $training = 'get' . ucfirst(self::$profileGroupTrainings[0]);
        if (count($jobSeeker->$training()) > 0) {
            self::$trainingStatus = 100;
        }

        self::$educationStatus = 0;
        $education = 'get' . ucfirst(self::$profileGroupEducation[0]);
        if (count($jobSeeker->$education()) > 0) {
            self::$educationStatus = 100;
        }

        self::$experienceStatus = 0;
        $experience = 'get' . ucfirst(self::$profileGroupProfessionals[0]);
        if (count($jobSeeker->$experience())) {
            self::$experienceStatus = 100;
        }

        $pgoCount = 0;
        foreach (self::$profileGroupOthers as $pgo) {
            $method = 'get' . ucfirst($pgo);
            if (count($jobSeeker->$method()) > 0) {
                $pgoCount++;
            }
        }
        self::$pgoCompletionStatus = ($pgoCount / count(self::$profileGroupOthers)) * 100;
    }

    public function getProfileCompletionStatus(JobSeeker $jobSeeker)
    {
        $this->setProfileCompletionStatus($jobSeeker);
        $total = self::$educationStatus + self::$pgpCompletionStatus + self::$pggCompletionStatus + self::$trainingStatus + self::$experienceStatus + self::$pgoCompletionStatus;
        $result = $total / 6;

        return $result;

    }

    public function canApplyForJob(JobSeeker $jobSeeker)
    {
        if ($jobSeeker->getProfileCompletedPercentage() >= 80 || $jobSeeker->getCurriculumVitaePath() != '') {
            return true;
        }

        return false;

    }


    public function getJobSeekerProfileStatus(JobSeeker $jobSeeker, $returnOverall = true)
    {
        $profileStatus = [
            JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_PERSONAL => 0,
            JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_GENERAL => 0,
            JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_EDUCATION => 0,
            JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_TRAINING => 0,
            JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_PROFESSIONAL => 0,
            JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_OTHER => 0,
            'overall' => 0,
        ];


        if( $jobSeeker and $jobSeeker->getProfileStatus() )
        {
            $profile = $this->getEntityManager()->getRepository(ProfileCompletion::class)->findOneBy([
                'id' => $jobSeeker->getProfileStatus()
            ]);

            if ($profile) {
                $profileStatus = [
                    JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_PERSONAL => $profile->getPersonal(),
                    JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_GENERAL => $profile->getGeneral(),
                    JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_EDUCATION => $profile->getEducation(),
                    JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_TRAINING => $profile->getTraining(),
                    JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_PROFESSIONAL => $profile->getProfessional(),
                    JobSeekerConstants::JOB_SEEKER_PROFILE_TYPE_OTHER => $profile->getOthers(),
                    'overall' => $profile->getOverall()
                ];

            }
        }

        if( !$returnOverall )
        {
            unset($profileStatus['overall']);
        }

        return $profileStatus;
    }
}
