<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('steven');
        $user->setPassword($this->encoder->encodePassword($user, 'azerty'));
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $this->addReference('steven', $user);

        $user2 = new User();
        $user2->setUsername('axel');
        $user2->setPassword($this->encoder->encodePassword($user2, 'azerty'));
        $user2->setRoles(['ROLE_USER']);
        $this->addReference('axel', $user2);

        $manager->persist($user);
        $manager->persist($user2);

        $manager->flush();
    }
}
