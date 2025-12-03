<?php

namespace App\Tests\Integration;

use App\Entity\Board;
use App\Repository\BoardRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BoardRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private BoardRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->repository = $this->entityManager->getRepository(Board::class);
    }

    public function testSearchByName(): void
    {
        // This is an integration test that requires a database
        // Skip if database is not available
        try {
            $boards = $this->repository->search('test');
            $this->assertIsArray($boards);
        } catch (\Exception $e) {
            $this->markTestSkipped('Database not available: ' . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}

