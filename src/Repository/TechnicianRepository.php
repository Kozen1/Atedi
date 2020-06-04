<?php

namespace App\Repository;

use App\Entity\Technician;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Technician|null find($id, $lockMode = null, $lockVersion = null)
 * @method Technician|null findOneBy(array $criteria, array $orderBy = null)
 * @method Technician[]    findAll()
 * @method Technician[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TechnicianRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Technician::class);
    }

    // /**
    //  * @return Technician[] Returns an array of Technician objects
    //  */

    public function findAll()
    {
        return $this->findBy(
            [],
            ['id' => 'DESC'],
        );
    }
}
