<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    const DEFAULT_USER = [
        'username' => 'notgabs@gmail.com',
        'password' => 'password'
    ];

    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $defaultUser = new User();
        $defaultUser->setPassword($this->encoder->encodePassword($defaultUser,self::DEFAULT_USER['password']));
        $defaultUser->setEmail(self::DEFAULT_USER['username'])
            ->setAge($faker->numberBetween(26, 34))
            ->setStatus(true);

        $manager->persist($defaultUser);

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email)
                ->setPassword($this->encoder->encodePassword($user, 'password'))
                ->setAge($faker->numberBetween(18, 75))
                ->setStatus($faker->randomElement([true, false]));

            $manager->persist($user);

            for ($j = 1; $j <= mt_rand(2, 5); $j++) {
                $article = new Article();
                $article->setAuthor($user)
                    ->setContent($faker->realText(150))
                    ->setName($faker->realText(30));

                $manager->persist($article);
            }
        }

        $manager->flush();
    }
}
