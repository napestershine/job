<?php
/**
 * Created by PhpStorm.
 * User: yarsha
 * Date: 2/22/17
 * Time: 11:36 AM
 */

namespace Yarsha\JobSeekerBundle\EventListener;

use Yarsha\JobSeekerBundle\Event\ProfileUpdateEvent;
use Yarsha\JobSeekerBundle\Service\JobSeekerProfileService;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class SeekerEventListener
 * @package Yarsha\JobSeekerBundle\EventListener
 * @DI\Service("yarsha.listener.seeker")
 * @DI\Tag(name="kernel.event_listener", attributes = {"event"="yarsha.event.seeker_profile_update", "method" = "onProfileUpdate"})
 */
class SeekerEventListener
{

    private $seekerService;

    /**
     * SeekerEventListener constructor.
     * @param JobSeekerProfileService $seekerService
     * @DI\InjectParams({
     *     "seekerService" = @DI\Inject("yarsha.service.jobseeker_profile")
     * })
     */
    public function __construct(JobSeekerProfileService $seekerService)
    {
        $this->seekerService = $seekerService;
    }

    public function onProfileUpdate(ProfileUpdateEvent $event)
    {
        $this->seekerService->updateProfileCompletion($event->getSeeker());
    }

}
