<?php

namespace App\Tests\Integration\Service;

use App\Enum\LockDownStatus;
use App\Factory\LockDownFactory;
use App\Service\GithubService;
use App\Service\LockDownHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Mailer\Test\InteractsWithMailer;
use Zenstruck\Messenger\Test\InteractsWithMessenger;
use function PHPUnit\Framework\assertSame;

class LockDownHelperTest extends KernelTestCase
{
    use ResetDatabase, Factories;
    use InteractsWithMailer;
    use InteractsWithMessenger;

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

        $this->getLockDownHelper()->endCurrentLockDown();
        assertSame($lockDown->getStatus(), LockDownStatus::ENDED);

    }

    public function testDinoEscapedPersistsLockDown(): void
    {
        self::bootKernel();

        $this->transport()->queue()->assertEmpty();

        $this->getLockDownHelper()->dinoEscaped();
        LockDownFactory::repository()->assert()->count(1);

        // Will make sure that at least one message was sent to the transport
        $this->transport()->processOrFail();
        //Equivalent to:
        //$this->transport()->queue()->assertCount(1);
        //$this->transport()->process();

        $this->mailer()->assertSentEmailCount(1);
        $this->mailer()->assertEmailSentTo('staff@dinotopia.com', 'PARK LOCKDOWN');
    }

    private function getLockDownHelper(): LockDownHelper
    {
        return self::getContainer()->get(LockDownHelper::class);
    }
}