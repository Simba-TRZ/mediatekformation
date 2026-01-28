<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 *  Contrôleur du Back-Office pour les playlists.
 */
#[IsGranted('ROLE_ADMIN')]
class BackofficePlaylistsController extends AbstractController
{
    private PlaylistRepository $playlistRepository;
    private EntityManagerInterface $entityManager;
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(
        PlaylistRepository $playlistRepository,
        EntityManagerInterface $entityManager,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->playlistRepository = $playlistRepository;
        $this->entityManager = $entityManager;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * Afficher la liste des playlists avec filtres et tri.
     */
    #[Route('/backoffice/playlists/', name: 'backoffice_playlists_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $champ = $request->query->get('champ', 'name');
        $ordre = $request->query->get('ordre', 'ASC');

        $playlists = $this->playlistRepository->findAllOrderByName($ordre);

        return $this->render('backoffice/playlists.html.twig', [
            'playlists' => $playlists,
            'champ' => $champ,
            'ordre' => $ordre,
        ]);
    }

    #[Route('/backoffice/playlists/add', name: 'backoffice_playlists_add')]
    public function add(Request $request): Response
    {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($playlist);
            $this->entityManager->flush();

            $this->addFlash('success', 'La playlist a bien été ajoutée.');
            return $this->redirectToRoute('backoffice_playlists_index');
        }

        return $this->render('backoffice/playlists_form.html.twig', [
            'formPlaylists' => $form->createView(),
        ]);
    }

    /**
     * Modifier une playlist.
     */
    #[Route('/backoffice/playlists/edit/{id}', name: 'backoffice_playlists_edit')]
    public function edit(int $id, Request $request): Response
    {
        $playlist = $this->playlistRepository->find($id);

        if (!$playlist) {
            throw $this->createNotFoundException("Playlist non trouvée");
        }

        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            // Message de succès
            $this->addFlash('success', 'La playlist a bien été modifiée');

            return $this->redirectToRoute('backoffice_playlists_index');
        }

        return $this->render('backoffice/playlists.edit.html.twig', [
            'formPlaylists' => $form->createView(),
            'playlist' => $playlist
        ]);
    }

    /**
     * Supprimer une playlist.
     */
    #[Route('/backoffice/playlists/delete/{id}', name: 'backoffice_playlists_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        $playlist = $this->playlistRepository->find($id);

        if (!$playlist) {
            $this->addFlash('error', 'Playlist non trouvée.');
            return $this->redirectToRoute('backoffice_playlists_index');
        }

        // Vérifier si la playlist a des formations rattachées
        if (!$playlist->getFormations()->isEmpty()) {
            $this->addFlash('error', 'Impossible de supprimer cette playlist car elle contient des formations.');
            return $this->redirectToRoute('backoffice_playlists_index');
        }

        try {
            // Suppression de la playlist
            $this->entityManager->remove($playlist);
            $this->entityManager->flush();

            // Message de succès
            $this->addFlash('success', 'La playlist a bien été supprimée.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }

        return $this->redirectToRoute('backoffice_playlists_index');
    }
}
