<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use App\Repository\CategorieRepository;
use App\Form\FormationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *  Contrôleur du Back-Office pour les formations.
 */
class BackofficeFormationsController extends AbstractController
{
    private FormationRepository $formationRepository;
    private PlaylistRepository $playlistRepository;
    private CategorieRepository $categorieRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        FormationRepository $formationRepository,
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->formationRepository = $formationRepository;
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Afficher la liste des formations avec filtres et tri.
     */
    #[Route('/backoffice/formations/', name: 'backoffice_formations_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $champ = $request->query->get('champ', 'title');
        $ordre = $request->query->get('ordre', 'ASC');
        $playlistId = $request->query->get('playlist');
        $categoryId = $request->query->get('category');
        $searchTitle = $request->query->get('title', '');

        // Récupération des formations selon les filtres
        if (!empty($searchTitle)) {
            $formations = $this->formationRepository->findByTitle($searchTitle, $champ, $ordre);
        } elseif (!empty($playlistId)) {
            $formations = $this->formationRepository->findByPlaylist($playlistId, $champ, $ordre);
        } elseif (!empty($categoryId)) {
            $formations = $this->formationRepository->findByCategory($categoryId, $champ, $ordre);
        } else {
            $formations = $this->formationRepository->findAllOrderBy($champ, $ordre);
        }

        return $this->render('backoffice/formations.html.twig', [
            'formations' => $formations,
            'playlists' => $this->playlistRepository->findAll(),
            'categories' => $this->categorieRepository->findAll(),
            'champ' => $champ,
            'ordre' => $ordre,
            'selectedPlaylist' => $playlistId,
            'selectedCategory' => $categoryId,
            'searchTitle' => $searchTitle,
        ]);
    }

    /**
     * Modifier une formation.
     */
    #[Route('/backoffice/formations/edit/{id}', name: 'backoffice_formations_edit')]
    public function edit(int $id, Request $request): Response
    {
        $formation = $this->formationRepository->find($id);
        $categories = $this->categorieRepository->findAll();

        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->formationRepository->add($formation);
            return $this->redirectToRoute('backoffice_formations_index');
        }

        return $this->render('backoffice/formations.edit.html.twig', [
            'formformations' => $form->createView(),
            'categories' => $categories,
            'formation' => $formation,
        ]);
    }

    /**
     * Supprimer une formation.
     */
    #[Route('/backoffice/formations/delete/{id}', name: 'backoffice_formations_delete', methods: ['POST'])]
    public function delete(int $id): Response
    {
        $formation = $this->formationRepository->find($id);
        if ($formation) {
            $this->entityManager->remove($formation);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('backoffice_formations_index');
    }

    /**
     * Recherche les formations contenant une valeur donnée.
     */
    #[Route('/backoffice/formations/recherche/{champ}', name: 'backoffice_formations_recherche', methods: ['GET'])]
    public function findAllContain(string $champ, Request $request): Response
    {
        $valeur = $request->query->get('recherche', '');
        $formations = $this->formationRepository->findByContainValue($champ, $valeur);
        $categories = $this->categorieRepository->findAll();

        return $this->render('backoffice/formations.html.twig', [
            'formations' => $formations,
            'categories' => $categories,
            'searchTitle' => $valeur
        ]);
    }
}
