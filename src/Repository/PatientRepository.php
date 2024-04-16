<?php

namespace App\Repository;

use App\Entity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Patient::class);
    }

    /**
     * Retrieve a summary of free patients with optional interest filtering.
     *
     * @param string|null $interestFilter
     * @return array
     */
    public function findFreePatientSummary(?string $interestFilter = null): array
    {
        $qb = $this->createQueryBuilder('p');

        $qb->select('p')
            ->andWhere('p.is_followed = 0')
            ->orderBy('p.registration_date');

        if ($interestFilter !== null) {
            // Utilisation d'une expression LIKE pour filtrer par intérêt
            $qb->andWhere($qb->expr()->like('p.interestedIn', ':interest'))
               ->setParameter('interest', '%' . $interestFilter . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
