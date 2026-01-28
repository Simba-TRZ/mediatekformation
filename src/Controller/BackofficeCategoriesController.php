<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur du Back-Office pour les catégories.
 */
#[Route('/backoffice/categories')]
#[IsGranted('ROLE_ADMIN')]
class BackofficeCategoriesController extends AbstractController
{
    private const FLASH_SUCCESS = 'success';
    private const FLASH_ERROR = 'error';
    private const CSRF_DELETE_PREFIX = 'delete';
    
    private CategorieRepository $categorieRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(CategorieRepository $categorieRepository, EntityManagerInterface $entityManager)
    {
        $this->categorieRepository = $categorieRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Afficher la liste des catégories et ajouter une nouvelle catégorie.
     */
    #[Route('/', name: 'backoffice_categories_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $categories = $this->categorieRepository->findAll();

        // Gestion de l'ajout d'une catégorie
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si la catégorie existe déjà
            $existingCategory = $this->categorieRepository->findOneBy(['name' => $categorie->getName()]);
            if ($existingCategory) {
                $this->addFlash(self::FLASH_ERROR, 'Cette catégorie existe déjà.');
            } else {
                $this->entityManager->persist($categorie);
                $this->entityManager->flush();
                $this->addFlash(self::FLASH_SUCCESS, 'La catégorie a bien été ajoutée.');
            }
            return $this->redirectToRoute('backoffice_categories_index');
        }

        return $this->render('backoffice/categories.html.twig', [
            'categories' => $categories,
            'formCategorie' => $form->createView(),
        ]);
    }

    /**
     * Supprimer une catégorie.
     */
    #[Route('/delete/{id}', name: 'backoffice_categories_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        $categorie = $this->categorieRepository->find($id);
        $submittedToken = (string) $request->request->get('_token');

        if (!$categorie) {
            $this->addFlash(self::FLASH_ERROR, 'Catégorie non trouvée.');
            return $this->redirectToRoute('backoffice_categories_index');
        }

        // Vérifier si la catégorie est rattachée à une formation
        if (!$categorie->getFormations()->isEmpty()) {
            $this->addFlash(self::FLASH_ERROR, 'Impossible de supprimer cette catégorie car elle est associée à une ou plusieurs formations.');
            return $this->redirectToRoute('backoffice_categories_index');
        }

        if ($this->isCsrfTokenValid(self::CSRF_DELETE_PREFIX . $categorie->getId(), $submittedToken)) {
            $this->entityManager->remove($categorie);
            $this->entityManager->flush();
            $this->addFlash(self::FLASH_SUCCESS, 'La catégorie a bien été supprimée.');
        } else {
            $this->addFlash(self::FLASH_ERROR, 'Token CSRF invalide. Suppression annulée.');
        }

        return $this->redirectToRoute('backoffice_categories_index');
    }
}
