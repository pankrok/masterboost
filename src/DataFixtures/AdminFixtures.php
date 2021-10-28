<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

class AdminFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setLogin('admin');
        $user->setRoles(['ROLE_SUPERADMIN']);
        $user->setPassword('admin');
        $user->setEmail('admin@admin.com');
        $user->setIsVerified(true);
        
        
        $manager->persist($user);

        $manager->flush();
    }
}
