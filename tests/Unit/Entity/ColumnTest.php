<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Board;
use App\Entity\Column;
use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    public function testColumnCreation(): void
    {
        $column = new Column();
        $column->setName('To Do');
        $column->setPosition(0);

        $this->assertNull($column->getId());
        $this->assertSame('To Do', $column->getName());
        $this->assertSame(0, $column->getPosition());
        $this->assertCount(0, $column->getTasks());
    }

    public function testAddTask(): void
    {
        $column = new Column();
        $column->setName('To Do');
        $column->setPosition(0);

        $task = new Task();
        $task->setTitle('Test Task');
        $task->setPosition(0);

        $column->addTask($task);

        $this->assertCount(1, $column->getTasks());
        $this->assertSame($column, $task->getColumn());
        $this->assertTrue($column->getTasks()->contains($task));
    }

    public function testRemoveTask(): void
    {
        $column = new Column();
        $column->setName('To Do');
        $column->setPosition(0);

        $task = new Task();
        $task->setTitle('Test Task');
        $task->setPosition(0);

        $column->addTask($task);
        $this->assertCount(1, $column->getTasks());

        $column->removeTask($task);
        $this->assertCount(0, $column->getTasks());
        $this->assertNull($task->getColumn());
    }

    public function testSetBoard(): void
    {
        $board = new Board();
        $board->setName('Test Board');

        $column = new Column();
        $column->setName('To Do');
        $column->setPosition(0);
        $column->setBoard($board);

        $this->assertSame($board, $column->getBoard());
    }
}

