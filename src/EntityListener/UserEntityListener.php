<?php

namespace App\EntityListener;

use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;

class UserEntityListener
{
    private $passwordHasher;
    private $pass;
    private $roles;

    public function __construct(UserPasswordHasherInterface  $passwordHasher)
    {
        $request = Request::createFromGlobals();
        $this->passwordHasher = $passwordHasher;
        $this->pass = $request->request->get('User')["password"] ?? '';
        $this->roles = $request->request->get('User')["roles"] ?? '';
    }

    public function prePersist(User $user, LifecycleEventArgs $event)
    {
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        ));
    }

    public function preUpdate(User $user, LifecycleEventArgs $event)
    {   
        if($this->pass) {
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                $this->pass
            ));
        }
        
        // if($this->roles) {
            // $user->setRoles($this->roles);
        // }
    }
}