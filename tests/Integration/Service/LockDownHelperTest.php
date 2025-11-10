<?php

namespace App\Tests\Integration\Service;

use App\Enum\LockDownStatus;
use App\Factory\LockDownFactory;
use App\Service\GithubService;
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

        // Partial mocking => real tests with the Github API whould be to complicated
        $githubService = $this->createMock(GithubService::class);
        $githubService->expects($this->once())
            ->method('clearLockDownAlerts');
        // Tell symfony to pass this mock service to LockDownHelper
        self::getContainer()->set(GithubService::class, $githubService);

        $lockDownHelper = self::getContainer()->get(LockDownHelper::class);

        $lockDownHelper->endCurrentLockDown($lockDown);
        assertSame($lockDown->getStatus(), LockDownStatus::ENDED);
    }
}