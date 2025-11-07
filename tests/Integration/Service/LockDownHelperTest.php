<?php

namespace App\Tests\Integration\Service;

use App\Enum\LockDownStatus;
use App\Factory\LockDownFactory;
use App\Service\LockDownHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use function PHPUnit\Framework\assertSame;

class LockDownHelperTest extends KernelTestCase
{
    use ResetDatabase, Factories;

    public function testEndCurrentLockDown(): void
    {
        self::bootKernel();

        $lockDown = LockDownFactory::createOne(
            [
                'status' => LockDownStatus::ACTIVE,
            ]
        );

        $lockDownHelper = self::getContainer()->get(LockDownHelper::class);

        $lockDownHelper->endCurrentLockDown($lockDown);

        assertSame($lockDown->getStatus(), LockDownStatus::ENDED);
    }
}