<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur du Back-Office pour les formations.
 */
#[Route('/backoffice/formations')]
#[IsGranted('ROLE_ADMIN')]
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
     * Afficher la liste des formations.
     */
    #[Route('/', name: 'backoffice_formations_index')]
    public function index(Request $request): Response
    {
        $formations = $this->formationRepository->findAllOrderBy('title', 'ASC');
        $playlists = $this->playlistRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        
        return $this->render('backoffice/formations.html.twig', [
            'formations' => $formations,
            'playlists' => $playlists,
            'categories' => $categories,
            'selectedPlaylist' => $request->get('playlist', ''),
            'selectedCategory' => $request->get('category', ''),
            'searchTitle' => $request->get('title', ''),
        ]);
    }

    /**
     * Afficher le formulaire d'édition d'une formation.
     */
    #[Route('/edit/{id}', name: 'backoffice_formations_edit')]
    public function edit(Request $request, Formation $formation): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Formation modifiée avec succès.');
            
            return $this->redirectToRoute('backoffice_formations_index');
        }

        return $this->render('backoffice/formations_form.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprimer une formation.
     */
    #[Route('/delete/{id}', name: 'backoffice_formations_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation): Response
    {
        $submittedToken = (string) $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete' . $formation->getId(), $submittedToken)) {
            $this->entityManager->remove($formation);
            $this->entityManager->flush();
            $this->addFlash('success', 'Formation supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('backoffice_formations_index');
    }

    /**
     * Recherche de formations.
     */
    #[Route('/recherche/{champ}', name: 'backoffice_formations_recherche')]
    public function findAllContain(string $champ, Request $request): Response
    {
        $valeur = $request->get('recherche');
        $formations = $this->formationRepository->findByContainValue($champ, $valeur);
        $playlists = $this->playlistRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        
        return $this->render('backoffice/formations.html.twig', [
            'formations' => $formations,
            'playlists' => $playlists,
            'categories' => $categories,
            'selectedPlaylist' => $request->get('playlist', ''),
            'selectedCategory' => $request->get('category', ''),
            'searchTitle' => $request->get('title', ''),
        ]);
    }
}
