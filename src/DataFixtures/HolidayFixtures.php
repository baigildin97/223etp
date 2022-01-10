<?php


namespace App\DataFixtures;


use App\Model\Admin\Entity\Holidays\Holiday;
use App\Model\Admin\Entity\Holidays\Id;
use App\Model\Flusher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class HolidayFixtures extends Fixture implements FixtureGroupInterface
{
    private $em;
    private $flusher;

    public function __construct(EntityManagerInterface $entityManager, Flusher $flusher)
    {
        $this->em = $entityManager;
        $this->flusher = $flusher;
    }

    public function load(ObjectManager $manager)
    {
        $holidays = [];
        $holidays[] = new Holiday(Id::next(), new \DateTimeImmutable('01.01.2021'), new \DateTimeImmutable('02.01.2021'));
        $holidays[] = new Holiday(Id::next(), new \DateTimeImmutable('04.01.2021'), new \DateTimeImmutable('09.01.2021'));
        $holidays[] = new Holiday(Id::next(), new \DateTimeImmutable('22.02.2021'), new \DateTimeImmutable('24.02.2021'));
        $holidays[] = new Holiday(Id::next(), new \DateTimeImmutable('08.03.2021'), new \DateTimeImmutable('09.03.2021'));
        $holidays[] = new Holiday(Id::next(), new \DateTimeImmutable('01.01.2021'), new \DateTimeImmutable('02.01.2021'));
        $holidays[] = new Holiday(Id::next(), new \DateTimeImmutable('03.05.2021'), new \DateTimeImmutable('04.05.2021'));
        $holidays[] = new Holiday(Id::next(), new \DateTimeImmutable('10.05.2021'), new \DateTimeImmutable('11.05.2021'));
        $holidays[] = new Holiday(Id::next(), new \DateTimeImmutable('14.06.2021'), new \DateTimeImmutable('15.06.2021'));
        $holidays[] = new Holiday(Id::next(), new \DateTimeImmutable('04.11.2021'), new \DateTimeImmutable('06.11.2021'));
        $holidays[] = new Holiday(Id::next(), new \DateTimeImmutable('31.12.2021'), new \DateTimeImmutable('01.01.2022'));

        foreach ($holidays as $holiday)
            $this->em->persist($holiday);

        $this->flusher->flush();
    }

    public static function getGroups(): array
    {
        return ['holidays'];
    }
}