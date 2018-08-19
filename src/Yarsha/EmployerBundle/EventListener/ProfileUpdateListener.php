<?php
/**
 * Created by PhpStorm.
 * User: yarsha
 * Date: 2/22/17
 * Time: 5:07 PM
 */

namespace Yarsha\EmployerBundle\EventListener;

use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\EmployerBundle\Event\ProfileUpdateEvent;
use Yarsha\EmployerBundle\Service\EmployerProfileService;

/**
 * Class ProfileUpdateListener
 * @package Yarsha\EmployerBundle\EventListener
 * @DI\Service("yarsha.listener.employer_profile")
 * @DI\Tag(name="kernel.event_listener", attributes={"event" = "yarsha.event.employer_profile_update", "method" = "onProfileUpdate"})
 *
 */
class ProfileUpdateListener
{

    private $employerService;

    /**
     * ProfileUpdateListener constructor.
     * @param EmployerProfileService $employerProfileService
     * @DI\InjectParams({
     *  "employerProfileService" = @DI\Inject("yarsha.service.employer_profile")
     * })
     */
    public function __construct(EmployerProfileService $employerProfileService)
    {
        $this->employerService = $employerProfileService;
    }

    public function onProfileUpdate(ProfileUpdateEvent $event)
    {
        $this->employerService->updateProfile($event->getEmployer());
    }

}
