<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Hospital;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HospitalFixture extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $hospital1 = new Hospital();
        $hospital1->name = 'Cluj County Emergency Clinical Hospital';
        $hospital1->city = 'Cluj-Napoca';
        $hospital1->address = 'Strada Clinicilor 3-5';
        $hospital1->setStartHour(new \DateTimeImmutable('08:00'));
        $hospital1->setEndHour(new \DateTimeImmutable('20:00'));
        $hospital1->description = 'The Cluj County Emergency Clinical Hospital is a public hospital in Cluj-Napoca, Romania. It is the largest hospital in Transylvania and one of the largest in Romania. It is also a teaching hospital, affiliated with the Iuliu Hațieganu University of Medicine and Pharmacy.';
        $hospital1->phone = '0264597852';
        $hospital1->email = 'secretariat@scjucluj.ro';
        $hospital1->postalCode = '400372';

        $manager->persist($hospital1);

        $hospital2 = new Hospital();
        $hospital2->name = 'Regina Maria';
        $hospital2->city = 'Cluj-Napoca';
        $hospital2->address = 'Calea Dorobantilor 29';
        $hospital2->setStartHour(new \DateTimeImmutable('08:00'));
        $hospital2->setEndHour(new \DateTimeImmutable('20:00'));
        $hospital2->description = 'Regina Maria is the largest private healthcare network in Romania, with 35 clinics and 5 hospitals in 17 cities across the country. The network includes 3,000 employees, 1,000 doctors and 1,000,000 patients per year.';
        $hospital2->phone = '0219268';
        $hospital2->email = '';
        $hospital2->postalCode = '400117';

        $manager->persist($hospital2);

        $manager->flush();
    }
}