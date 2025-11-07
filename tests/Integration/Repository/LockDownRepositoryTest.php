<?php

namespace App\Tests\Integration\Repository;

use App\Entity\LockDown;
use App\Enum\LockDownStatus;
use App\Factory\LockDownFactory;
use App\Repository\LockDownRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class LockDownRepositoryTest extends KernelTestCase
{
    use ResetDatabase, Factories;

    public function testIsInLockDownWithNoLockDownRows(): void
    {

        // Initialize Symfony (which starts the service container)
        // No Dependency Injection in test environment
        self::bootKernel();

        LockDownFactory::createOne([
            'createdAt' => new \DateTimeImmutable('-1 day'),
            'status' => LockDownStatus::ACTIVE,
        ]);

        LockDownFactory::createMany(5, [
            'createdAt' => new \DateTimeImmutable('-2 day'),
            'status' => LockDownStatus::ENDED,
        ]);

        $this->assertTrue($this->getlockDownRepository()->isInLockDown());
    }

    public function testIsInLockDownReturnsFalseIfMostRecentIsNotActive(): void
    {

        // Initialize Symfony (which starts the service container)
        // No Dependency Injection in test environment
        self::bootKernel();

        LockDownFactory::createOne([
            'createdAt' => new \DateTimeImmutable('-1 day'),
            'status' => LockDownStatus::ENDED,
        ]);

        LockDownFactory::createMany(5, [
            'createdAt' => new \DateTimeImmutable('-2 day'),
            'status' => LockDownStatus::ACTIVE,
        ]);

        $this->assertFalse($this->getlockDownRepository()->isInLockDown());
    }

    public function testIsInLockDownReturnsTrueIfMostRecentLockDownIs()
    {
        self::bootKernel();

        $lockDown = new LockDown();
        $lockDown->setReason('Dinos have organized their own lunch break');
        $lockDown->setCreatedAt(new \DateTimeImmutable('-1 day'));

        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $entityManager->persist($lockDown);
        $entityManager->flush();

        self::assertTrue($this->getlockDownRepository()->isInLockDown());
    }

    private function getlockDownRepository(): LockDownRepository
    {
        return self::getContainer()->get(LockDownRepository::class);
    }
}