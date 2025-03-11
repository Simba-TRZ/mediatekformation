<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Playlist>
 */
class PlaylistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    public function add(Playlist $entity): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush();
    }

    public function remove(Playlist $entity): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($entity);
        $entityManager->flush();
    }

    /**
     * Retourne toutes les playlists triées par nom.
     *
     * @param string $ordre L'ordre de tri (ASC ou DESC)
     * @return Playlist[]
     */
    public function findAllOrderByName(string $ordre): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.formations', 'f')
            ->groupBy('p.id')
            ->orderBy('p.name', $ordre)
            ->getQuery()
            ->getResult();
    }

    public function findAllSortedBy($champ, $ordre): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->leftJoin('p.formations', 'f')
            ->groupBy('p.id')
            ->select('p as playlist, COUNT(f.id) as formations_count');


        if ($champ === 'formations_count') {
            $queryBuilder->orderBy('formations_count', $ordre);
        } else {
            $queryBuilder->orderBy('p.'.$champ, $ordre);
        }
        

        return $queryBuilder->getQuery()->getResult();
    }



    /**
     * Recherche les playlists contenant une valeur dans un champ donné.
     *
     * @param string $champ Le champ à rechercher
     * @param string|null $valeur La valeur à chercher (optionnelle)
     * @param string|null $table Nom de la table si le champ appartient à une autre entité (optionnelle)
     * @return Playlist[]
     */
    public function findByContainValue(string $champ, ?string $valeur, ?string $table = null): array
    {
        if (empty($valeur)) {
            return $this->findAllOrderByName('ASC');
        }

        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.formations', 'f');

        if ($table) {
            $qb->leftJoin('f.categories', 'c')
               ->where("c.$champ LIKE :valeur");
        } else {
            $qb->where("p.$champ LIKE :valeur");
        }

        return $qb->setParameter('valeur', "%$valeur%")
                  ->groupBy('p.id')
                  ->orderBy('p.name', 'ASC')
                  ->getQuery()
                  ->getResult();
    }
}
