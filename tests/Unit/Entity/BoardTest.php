<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Board;
use App\Entity\Column;
use PHPUnit\Framework\TestCase;

class BoardTest extends TestCase
{
    public function testBoardCreation(): void
    {
        $board = new Board();
        $board->setName('Test Board');

        $this->assertNull($board->getId());
        $this->assertSame('Test Board', $board->getName());
        $this->assertCount(0, $board->getColumns());
    }

    public function testAddColumn(): void
    {
        $board = new Board();
        $board->setName('Test Board');

        $column = new Column();
        $column->setName('To Do');
        $column->setPosition(0);

        $board->addColumn($column);

        $this->assertCount(1, $board->getColumns());
        $this->assertSame($board, $column->getBoard());
        $this->assertTrue($board->getColumns()->contains($column));
    }

    public function testRemoveColumn(): void
    {
        $board = new Board();
        $board->setName('Test Board');

        $column = new Column();
        $column->setName('To Do');
        $column->setPosition(0);

        $board->addColumn($column);
        $this->assertCount(1, $board->getColumns());

        $board->removeColumn($column);
        $this->assertCount(0, $board->getColumns());
        $this->assertNull($column->getBoard());
    }

    public function testTimestamps(): void
    {
        $board = new Board();
        $board->setName('Test Board');

        // Simulate PrePersist
        $board->onPrePersist();

        $this->assertInstanceOf(\DateTimeImmutable::class, $board->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $board->getUpdatedAt());
    }

    public function testUpdateTimestamp(): void
    {
        $board = new Board();
        $board->setName('Test Board');
        $board->onPrePersist();

        $originalUpdatedAt = $board->getUpdatedAt();

        // Simulate PreUpdate
        sleep(1); // Ensure timestamp difference
        $board->onPreUpdate();

        $this->assertGreaterThan($originalUpdatedAt, $board->getUpdatedAt());
    }
}

