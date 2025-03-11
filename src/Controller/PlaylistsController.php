<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur des playlists
 */
class PlaylistsController extends AbstractController
{
    private const PLAYLISTS_VIEW = "pages/playlists.html.twig"; // Constante pour éviter la duplication de chaîne

    private PlaylistRepository $playlistRepository;
    private FormationRepository $formationRepository;
    private CategorieRepository $categorieRepository;

    public function __construct(
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        FormationRepository $formationRepository
    ) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRepository;
    }

    #[Route('/playlists', name: 'playlists')]
    public function index(): Response
    {
        return $this->render(self::PLAYLISTS_VIEW, [
            'playlists' => $this->playlistRepository->findAllOrderByName('ASC'),
            'categories' => $this->categorieRepository->findAll(),
        ]);
    }

    #[Route('/playlists/tri/{champ}/{ordre}', name: 'playlists.sort')]
    public function sort(PlaylistRepository $repository, string $champ, string $ordre): Response
    {
        $playlists = $repository->findAllSortedBy($champ, $ordre);

        return $this->render(self::PLAYLISTS_VIEW, [
            'playlists' => $playlists,
            'categories' => $this->categorieRepository->findAll(),
        ]);
    }


    #[Route('/playlists/recherche/{champ}/{table}', name: 'playlists.findallcontain')]
    public function findAllContain(string $champ, Request $request, string $table = ""): Response
    {
        $valeur = $request->query->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);

        return $this->render(self::PLAYLISTS_VIEW, [
            'playlists' => $playlists,
            'categories' => $this->categorieRepository->findAll(),
            'valeur' => $valeur,
            'table' => $table,
        ]);
    }

    #[Route('/playlists/playlist/{id}', name: 'playlists.showone')]
    public function showOne(int $id): Response
    {
        return $this->render("pages/playlist.html.twig", [
            'playlist' => $this->playlistRepository->find($id),
            'playlistcategories' => $this->categorieRepository->findAllForOnePlaylist($id),
            'playlistformations' => $this->formationRepository->findAllForOnePlaylist($id),
        ]);
    }
}
