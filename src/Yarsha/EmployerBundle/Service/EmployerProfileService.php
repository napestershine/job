<?php
/**
 * Created by PhpStorm.
 * User: yarsha
 * Date: 2/22/17
 * Time: 4:36 PM
 */

namespace Yarsha\EmployerBundle\Service;


use Yarsha\MainBundle\Service\AbstractService;
use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\EmployerBundle\Entity\User as Employer;
use Yarsha\OrganizationBundle\Entity\Organization;

/**
 * Class EmployerProfileService
 * @package Yarsha\EmployerBundle\Service
 * @DI\Service("yarsha.service.employer_profile", parent="yarsha.service.abstract")
 * @DI\Tag(name="yarsha.abstract_service")
 */
class EmployerProfileService extends AbstractService
{

    public static $organizationInformation = [
        "name",
        "category",
        "ownershipType",
        "size",
        "profile",
        "phone",
        "address"
    ];

    public static $contactPersonDetail = [
        "firstName",
        "lastName",
        "phone",
        "email",
        "designation"
    ];

    public static $organizationInformationStatus;

    public static $contactPersonStatus;

    public static $overall;

    public function updateProfile(Employer $employer)
    {
        $this->setProfileCompletionStatus($employer);
        $em = $this->getEntityManager();
        $organization = $employer->getOrganization();
        if (!$organization) {
            $organization = new Organization();
            $employer->setOrganization($organization);
        }
        $organization->setProfileCompletedPercentage(self::$organizationInformationStatus);
        $em->persist($employer);
        $em->flush();
    }

    public function setProfileCompletionStatus(Employer $employer)
    {
        $oiCount = 0;
        foreach (self::$organizationInformation as $o) {
            $method = 'get' . ucfirst($o);
            if ($employer->getOrganization()->$method() != "") {
                $oiCount++;
            }
        }
        self::$organizationInformationStatus = ($oiCount / count(self::$organizationInformation)) * 100;

        $cpCount = 0;
        foreach (self::$contactPersonDetail as $c) {
//            $method = 'get' . ucfirst($c);
            if (count($employer->getOrganization()->getContactPersons()) > 0) {
                $cpCount++;
            }
        }
        self::$contactPersonStatus = ($cpCount / count(self::$contactPersonDetail)) * 100;
        $total = self::$organizationInformationStatus + self::$contactPersonStatus;
        self::$overall = $total / 2;
    }

    public function getProfileStatus(Employer $employer)
    {
        $this->setProfileCompletionStatus($employer);

        return self::$overall;
    }

}
