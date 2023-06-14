<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->plainPassword = 'Parola123';
        $admin->password = $this->userPasswordHasher->hashPassword($admin, $admin->plainPassword);
        $admin->email = 'gireada_ovidiu@test.com';
        $admin->role = 'ROLE_ADMIN';
        $admin->firstName = 'Ovidiu';
        $admin->lastName = 'Gireada';
        $admin->telephoneNr = '0754281716';
        $admin->cnp = '5010911070069';
        $manager->persist($admin);

        $medic = new User();
        $medic->plainPassword = 'Parola123';
        $medic->password = $this->userPasswordHasher->hashPassword($medic, $medic->plainPassword);
        $medic->email = 'isabel_opris@test.com';
        $medic->role = 'ROLE_MEDIC';
        $medic->firstName = 'Isabel';
        $medic->lastName = 'Opris';
        $medic->telephoneNr = '0747390116';
        $medic->cnp = '6010920055084';
        $medic->experience = 5;
        $medic->specialization = 'Cardiologie';

        $manager->persist($medic);

        $medic2 = new User();
        $medic2->plainPassword = 'Parola123';
        $medic2->password = $this->userPasswordHasher->hashPassword($medic2, $medic2->plainPassword);
        $medic2->email = 'razvan_gireada@test.com';
        $medic2->role = 'ROLE_MEDIC';
        $medic2->firstName = 'Razvan';
        $medic2->lastName = 'Gireada';
        $medic2->telephoneNr = '0743325705';
        $medic2->cnp = '5000305078683';
        $medic2->experience = 10;
        $medic2->specialization = 'Stomatologie';

        $manager->persist($medic2);

        $medic3 = new User();
        $medic3->plainPassword = 'Parola123';
        $medic3->password = $this->userPasswordHasher->hashPassword($medic3, $medic3->plainPassword);
        $medic3->email = 'cristi_gireada@test.com';
        $medic3->role = 'ROLE_MEDIC';
        $medic3->firstName = 'Cristi';
        $medic3->lastName = 'Gireada';
        $medic3->telephoneNr = '0744533647';
        $medic3->cnp = '1830829076418';
        $medic3->experience = 15;
        $medic3->specialization = 'Chirurgie';

        $manager->persist($medic3);

        $manager->flush();
    }
}
