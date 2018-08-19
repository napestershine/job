<?php
/**
 * Created by PhpStorm.
 * User: yarsha
 * Date: 2/22/17
 * Time: 5:07 PM
 */

namespace Yarsha\EmployerBundle\Event;


use Symfony\Component\EventDispatcher\Event;
use Yarsha\EmployerBundle\Entity\User as Employer;

class EmployerEvent extends Event
{

    private $employer;

    public function __construct(Employer $employer)
    {
        $this->employer = $employer;
    }

    public function getEmployer()
    {
        return $this->employer;
    }

}
