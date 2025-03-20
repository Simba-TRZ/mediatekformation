<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formation>
 */
class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }
    
    public function findByTitle(string $title, string $champ, string $ordre)
    {
        return $this->createQueryBuilder('f')
            ->where('f.title LIKE :title')
            ->setParameter('title', '%' . $title . '%')
            ->orderBy('f.' . $champ, $ordre)
            ->getQuery()
            ->getResult();
    }        

    public function add(Formation $entity): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush();
    }

    public function remove(Formation $entity): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($entity);
        $entityManager->flush();
    }
    
    /**
     * Retourne les n formations les plus récentes
     * @param type $nb
     * @return Formation[]
     */
    public function findAllLasted($nb) : array {
        return $this->createQueryBuilder('f')
                ->orderBy('f.publishedAt', 'DESC')
                ->setMaxResults($nb)     
                ->getQuery()
                ->getResult();
    }

    /**
     *  Retourner toutes les formations triées sur un champ donné.
     *
     * @param string $champ Le champ sur lequel trier
     * @param string $ordre L'ordre de tri (ASC ou DESC)
     * @return Formation[]
     */
    public function findAllOrderBy(string $champ, string $ordre): array
    {
        $allowedFields = ['title', 'date', 'playlist'];
        if (!in_array($champ, $allowedFields)) {
            $champ = 'title';
        }
        return $this->createQueryBuilder('f')
            ->orderBy("f.$champ", strtoupper($ordre) === 'DESC' ? 'DESC' : 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     *  Retourner toutes les formations liées à une catégorie spécifique.
     *
     * @param int $playlistId L'ID de la playlist
     * @param string $champ champ de tri
     * @param string $ordre ordre de tri
     * @return Formation[]
     */
    public function findByPlaylist(int $playlistId, string $champ = 'title', string $ordre = 'ASC'): array
    {
        return $this->createQueryBuilder('f')
                ->join('f.playlist', 'p')
                ->where('p.id = :playlistId')
                ->setParameter('playlistId', $playlistId)
                ->orderBy("f.$champ", strtoupper($ordre) === 'DESC' ? 'DESC' : 'ASC')
                ->getQuery()
                ->getResult();
    }
    
    /**
     * Retourner toutes les formations liées à une catégorie spécifique.
     *
     * @param int $categoryId L'ID de la catégorie
     * @param string $champ Champ de tri
     * @param string $ordre Ordre de tri
     * @return Formation[]
     */
    public function findByCategory(int $categoryId, string $champ = 'title', string $ordre = 'ASC'): array
    {
        return $this->createQueryBuilder('f')
            ->join('f.categories', 'c')
            ->where('c.id = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->orderBy("f.$champ", strtoupper($ordre) === 'DESC' ? 'DESC' : 'ASC')  
            ->getQuery()
            ->getResult();
    }

    /**
     * Rechercher les formations contenant une valeur dans un champ donné.
     *
     * @param string $champ Le champ à rechercher
     * @param string|null $valeur La valeur à chercher
     * @return Formation[]
     */
    public function findByContainValue(string $champ, ?string $valeur): array
    {
        if (empty($valeur)) {
            return $this->findAll();
        }

        return $this->createQueryBuilder('f')
            ->where("f.$champ LIKE :valeur")
            ->setParameter('valeur', "%$valeur%")
            ->orderBy('f.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Retourne la liste des formations d'une playlist
     * @param type $idPlaylist
     * @return array
     */
    public function findAllForOnePlaylist($idPlaylist): array{
        return $this->createQueryBuilder('f')
                ->join('f.playlist', 'p')
                ->where('p.id=:id')
                ->setParameter('id', $idPlaylist)
                ->orderBy('f.publishedAt', 'ASC')   
                ->getQuery()
                ->getResult();        
    }
}
