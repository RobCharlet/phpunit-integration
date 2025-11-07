<?php

namespace App\Service;

use App\Entity\LockDown;
use App\Enum\LockDownStatus;
use App\Repository\LockDownRepository;
use Doctrine\ORM\EntityManagerInterface;

class LockDownHelper
{
    public function __construct(
        private LockDownRepository $lockDownRepository,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function EndCurrentLockDown(): void
    {
        $lockDown = $this->lockDownRepository->findMostRecent();

        if (!$lockDown) {
            throw new \LogicException("There is no lock down to end.");
        }

        $lockDown->setStatus(LockDownStatus::ENDED);
        $this->entityManager->flush();
    }
}