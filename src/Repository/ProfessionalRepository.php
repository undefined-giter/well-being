<?php

namespace App\Repository;

use App\Entity\Professional;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProfessionalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Professional::class);
    }

    /**
     * Retrieve a summary of registered professionals with optional interest filtering.
     *
     * @param string|null $interestFilter
     * @return array
     */
    public function findProfessionalSummary(?string $interestFilter): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('p.first_name', 'p.last_name', 'p.specialization', 'p.slug', 'p.online_availability', 'p.location')
            ->orderBy('p.registration_date','DESC');

        if ($interestFilter !== null) {
            $queryBuilder->andWhere('p.specialization LIKE :interest')
                ->setParameter('interest', '%' . $interestFilter . '%');
        }

        $query = $queryBuilder->getQuery();
        $professionals = $query->getResult();

        $summary = [];
        foreach ($professionals as $professional) {
            $summary[] = [
                'first_name' => $professional['first_name'],
                'last_name' => $professional['last_name'],
                'specialization' => $professional['specialization'],
                'slug' => $professional['slug'],
                'online_availability' => $professional['online_availability'],
                'location' => $professional['location'],
            ];
        }

        return $summary;
    }
}
