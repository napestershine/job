<?php
/**
 * Created by PhpStorm.
 * User: yarsha
 * Date: 2/22/17
 * Time: 5:07 PM
 */

namespace Yarsha\EmployerBundle\EventListener;

use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\EmployerBundle\Event\EmployerEvent;
use Yarsha\EmployerBundle\Service\EmployerService;

/**
 * Class ProfileUpdateListener
 * @package Yarsha\EmployerBundle\EventListener
 * @DI\Service("yarsha.listener.employer")
 * @DI\Tag(name="kernel.event_listener", attributes={"event" = "yarsha_employer.job.posted", "method" = "onJobPost"})
 *
 */
class EmployerEventListener
{

    private $employerService;

    /**
     * ProfileUpdateListener constructor.
     * @param EmployerService
     * @DI\InjectParams({
     *  "employerService" = @DI\Inject("yarsha.service.employer")
     * })
     */
    public function __construct(EmployerService $employerService)
    {
        $this->employerService = $employerService;
    }

    public function onJobPost(EmployerEvent $event)
    {
        $this->employerService;
    }

}
