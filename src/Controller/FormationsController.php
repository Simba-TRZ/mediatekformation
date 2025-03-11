<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Formation;
use App\Form\FormationType;

/**
 * Contrôleur des formations.
 *
 * @author emds
 */
class FormationsController extends AbstractController
{
    private const TEMPLATE_FORMATIONS = 'pages/formations.html.twig';
    private const TEMPLATE_FORMATION = 'pages/formation.html.twig';

    private FormationRepository $formationRepository;
    private CategorieRepository $categorieRepository;

    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }

    /**
     * Affiche la liste des formations.
     */
    #[Route('/formations', name: 'formations')]
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();

        return $this->render(self::TEMPLATE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories,
        ]);
    }

    /**
     * Trie les formations par un champ donné.
     *
     * @param string $champ Le champ sur lequel trier
     * @param string $ordre L'ordre de tri (ASC ou DESC)
     * @param string|null $table La table associée si nécessaire
     */
    #[Route('/formations/tri/{champ}/{ordre}/{table}', name: 'formations.sort')]
    public function sort(string $champ, string $ordre, ?string $table = null): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();

        return $this->render(self::TEMPLATE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories,
        ]);
    }

    /**
     * Recherche les formations contenant une valeur donnée.
     *
     * @param string $champ Le champ à rechercher
     * @param Request $request La requête contenant le terme de recherche
     * @param string|null $table La table associée si nécessaire
     */
    #[Route('/formations/recherche/{champ}', name: 'formations.findallcontain')]
    public function findAllContain(string $champ): Response
    {
        $valeur = $_REQUEST['recherche'];
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, null);
        $categories = $this->categorieRepository->findAll();

        return $this->render(self::TEMPLATE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * Affiche une formation spécifique.
     *
     * @param int $id L'identifiant de la formation
     */
    #[Route('/formations/formation/{id}', name: 'formations.showone')]
    public function showOne(int $id): Response
    {
        $formation = $this->formationRepository->find($id);

        return $this->render(self::TEMPLATE_FORMATION, [
            'formation' => $formation,
        ]);
    }
}
