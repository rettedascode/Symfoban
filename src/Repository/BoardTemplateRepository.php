<?php

namespace App\Repository;

use App\Entity\BoardTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BoardTemplate>
 *
 * @method BoardTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method BoardTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method BoardTemplate[]    findAll()
 * @method BoardTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoardTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoardTemplate::class);
    }
}

