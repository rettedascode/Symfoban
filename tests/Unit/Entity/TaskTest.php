<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Column;
use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testTaskCreation(): void
    {
        $task = new Task();
        $task->setTitle('Test Task');
        $task->setDescription('Test Description');
        $task->setPosition(0);

        $this->assertNull($task->getId());
        $this->assertSame('Test Task', $task->getTitle());
        $this->assertSame('Test Description', $task->getDescription());
        $this->assertSame(0, $task->getPosition());
    }

    public function testPriority(): void
    {
        $task = new Task();
        $task->setTitle('Test Task');

        $priorities = ['low', 'medium', 'high', 'critical'];

        foreach ($priorities as $priority) {
            $task->setPriority($priority);
            $this->assertSame($priority, $task->getPriority());
        }
    }

    public function testDueDate(): void
    {
        $task = new Task();
        $task->setTitle('Test Task');

        $dueDate = new \DateTime('2024-12-31');
        $task->setDueDate($dueDate);

        $this->assertSame($dueDate, $task->getDueDate());
    }

    public function testIsOverdue(): void
    {
        $task = new Task();
        $task->setTitle('Test Task');

        // Past due date
        $pastDate = new \DateTime('yesterday');
        $task->setDueDate($pastDate);
        $this->assertTrue($task->isOverdue());

        // Future due date
        $futureDate = new \DateTime('+1 week');
        $task->setDueDate($futureDate);
        $this->assertFalse($task->isOverdue());

        // No due date
        $task->setDueDate(null);
        $this->assertFalse($task->isOverdue());
    }

    public function testIsDueSoon(): void
    {
        $task = new Task();
        $task->setTitle('Test Task');

        // Due in 2 days (within 3 days)
        $soonDate = new \DateTime('+2 days');
        $task->setDueDate($soonDate);
        $this->assertTrue($task->isDueSoon());

        // Due in 5 days (outside 3 days)
        $laterDate = new \DateTime('+5 days');
        $task->setDueDate($laterDate);
        $this->assertFalse($task->isDueSoon());

        // Past due date
        $pastDate = new \DateTime('yesterday');
        $task->setDueDate($pastDate);
        $this->assertFalse($task->isDueSoon());

        // No due date
        $task->setDueDate(null);
        $this->assertFalse($task->isDueSoon());
    }

    public function testAddTag(): void
    {
        $task = new Task();
        $task->setTitle('Test Task');

        $tag = $this->createMock(\App\Entity\Tag::class);
        $task->addTag($tag);

        $this->assertCount(1, $task->getTags());
        $this->assertTrue($task->getTags()->contains($tag));
    }

    public function testRemoveTag(): void
    {
        $task = new Task();
        $task->setTitle('Test Task');

        $tag = $this->createMock(\App\Entity\Tag::class);
        $task->addTag($tag);
        $this->assertCount(1, $task->getTags());

        $task->removeTag($tag);
        $this->assertCount(0, $task->getTags());
    }
}

