<?php
/**
 * Created by PhpStorm.
 * User: yarsha
 * Date: 2/22/17
 * Time: 11:37 AM
 */

namespace Yarsha\JobSeekerBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Yarsha\JobSeekerBundle\Entity\User as JobSeeker;

class ProfileUpdateEvent extends Event
{

    protected $jobSeeker;

    public function __construct(JobSeeker $jobSeeker)
    {
        $this->jobSeeker = $jobSeeker;
    }

    public function getSeeker()
    {
        return $this->jobSeeker;
    }

}
