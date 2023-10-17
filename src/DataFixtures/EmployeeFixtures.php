<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EmployeeFixtures extends Fixture
{
    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; ++$i) {
            $employee = new Employee();
            $employee->setName('Name'.$i);
            $employee->setSurname('Surname'.$i);
            $employee->setEmail("email$i@example.com");
            $employee->setHiredAt(new \DateTimeImmutable('-'.rand(0, 10).'days'));
            $employee->setCurrentSalaryAmount((string) (100 + $i * 10));

            $manager->persist($employee);
        }

        $manager->flush();
    }
}
