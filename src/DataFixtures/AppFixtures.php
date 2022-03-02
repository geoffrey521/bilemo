<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Factory\CustomerFactory;
use App\Factory\PhoneFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $customer = new Customer();
        $customer->setEmail('test@mail.com')
            ->setContactFirstname('Bob')
            ->setContactLastname('Sinclar')
            ->setPhoneNumber('0148475062')
            ->setSociety('DFiscount');
        $manager->persist($customer);

        CustomerFactory::createMany(
            '5',
            [
                'users' => UserFactory::new()->many(10, 20)
            ]
        );

        PhoneFactory::createMany(10);

        $manager->flush();
    }
}
