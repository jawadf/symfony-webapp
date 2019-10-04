<?php

namespace App\DataFixtures;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }


    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $user = new User();
        $manager->persist($user);
        $user->setEmail($this->email = 'hello@gmail.com');
        $user->setRoles($this->roles = ['ROLE_ADMIN']);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'the_new_password'
        ));


        $user = new User();
        $manager->persist($user);
        $user->setEmail($this->email = 'hey@gmail.com');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'hey123'
        ));

        $manager->flush();
    }
}
