<?php

namespace App\Tests\Unit\Service;

use App\Entity\ActivityLog;
use App\Entity\User;
use App\Service\ActivityLogService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ActivityLogServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private ActivityLogService $service;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->service = new ActivityLogService($this->entityManager);
    }

    public function testLogWithoutUser(): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (ActivityLog $log) {
                return $log->getAction() === 'test_action'
                    && $log->getEntityType() === 'Task'
                    && $log->getEntityId() === 123
                    && $log->getDescription() === 'Test description'
                    && $log->getUser() === null;
            }));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->service->log(
            'test_action',
            'Task',
            123,
            'Test description',
            null
        );
    }

    public function testLogWithUser(): void
    {
        $user = $this->createMock(User::class);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (ActivityLog $log) use ($user) {
                return $log->getAction() === 'create'
                    && $log->getEntityType() === 'Board'
                    && $log->getEntityId() === 456
                    && $log->getDescription() === 'Created new board'
                    && $log->getUser() === $user;
            }));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->service->log(
            'create',
            'Board',
            456,
            'Created new board',
            $user
        );
    }

    public function testLogWithMinimalData(): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (ActivityLog $log) {
                return $log->getAction() === 'delete'
                    && $log->getEntityType() === null
                    && $log->getEntityId() === null
                    && $log->getDescription() === null
                    && $log->getUser() === null;
            }));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->service->log('delete');
    }
}

