<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Factory\CustomerFactory;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        CustomerFactory::createMany(
            '5',
            [
                'users' => UserFactory::new()->many(10, 20)
            ]
        );

        $customer = new Customer();
        $customer->setSociety('Phancy')
            ->setPhoneNumber('09847261873')
            ->setContactLastname('Earmann')
            ->setContactFirstname('Hellott')
            ->setEmail('eh.phancy@test.test')
            ->setPassword('test');

        $manager->persist($customer);


        ProductFactory::createMany(10);

        $manager->flush();
    }
}
