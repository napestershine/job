<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Yarsha\OrganizationBundle\Entity\OrganizationType;

class LoadOrganizationTypeData extends AbstractFixture implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
//        $faker = \Faker\Factory::create();
//        $job = new Job();
//        $job->addLocation($faker->address);
//        $job->setCategory($this->addReference(1));
//        $manager->persist($job);
//        $manager->flush();
    }
}
