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
     */
    public function findAllOrderByName($ordre): array
    {   
        return $this->createQueryBuilder('p')
                ->orderBy('p.name', $ordre)
                ->getQuery()
                ->getResult();
    }

    /**
     * Trie les playlists par champ.
     */
    public function findAllSortedBy($champ, $ordre): array
    {
        $allowedFields = ['name', 'description'];
        if (!in_array($champ, $allowedFields)) {
            $champ = 'name';
        }

        return $this->createQueryBuilder('p')
            ->orderBy('p.' . $champ, $ordre)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche les playlists contenant une valeur dans un champ donné.
     */

    public function findByContainValue(string $champ, ?string $valeur, ?string $table = null): array
    {
        if (empty($valeur)) {
            return $this->findAllOrderByName('ASC');
        }

        // Vérifier que le champ est autorisé
        $allowedFields = ['name', 'description'];
        if (!in_array($champ, $allowedFields)) {
            throw new \InvalidArgumentException("Champ non valide : $champ");
        }

        $qb = $this->createQueryBuilder('p');

        if ($table === 'categories') {
            $qb->leftJoin('p.formations', 'f')
               ->leftJoin('f.categories', 'c')
               ->andWhere("c." . $champ . " LIKE :valeur");
        } else {
            $qb->andWhere("p." . $champ . " LIKE :valeur");
        }

        return $qb->setParameter('valeur', '%' . $valeur . '%')
              ->orderBy('p.name', 'ASC')
              ->getQuery()
              ->getResult();
    }
}