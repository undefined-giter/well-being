<?php

namespace App\Repository;

use App\Entity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Patient>
 *
 * @method Patient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Patient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Patient[]    findAll()
 * @method Patient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Patient::class);
    }

    /**
     * Retourne un résumé des patients triés par date d'inscription décroissante
     *
     * @return array
     */
    public function findPatientSummary(): array
    {
        return $this->createQueryBuilder('pa')
            ->select('pa.firstName', 'pa.lastName', 'pa.description', 'pa.slug')
            ->orderBy('pa.registrationDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
