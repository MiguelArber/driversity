<?php

namespace App\DataFixtures;

use App\Entity\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Faker;

class DataFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('es_ES');
        $populator = new Faker\ORM\Doctrine\Populator($faker, $em);
        $populator->addEntity(User::class, 10);
        $insertedPKs = $populator->execute();

        // $user = new User();
        // $user->setUsername('tomas2');
        // $user->setPassword('123456789');
        // $user->setEmail('tomas2@gmail.com');
        // $user->setTimeFlex('2');
        // $user->setLocationFlex('2');
        // $manager->persist($user);
        // $manager->flush();
    }
}
