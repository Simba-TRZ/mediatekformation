<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    /**
     * Début du chemin vers les images YouTube.
     */
    private const CHEMIN_IMAGE = "https://i.ytimg.com/vi/";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\LessThanOrEqual("today", message: "La date ne peut pas être postérieure à aujourd'hui.")]
    private ?\DateTimeInterface $publishedAt = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $videoId = null;

    #[ORM\ManyToOne(inversedBy: 'formations', cascade: ['persist'])]
    private ?Playlist $playlist = null;

    /**
     * @var Collection<int, Categorie>
     */
    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'formations')]
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    /**
     * Retourne l'ID de la formation.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne la date de publication.
     */
    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    /**
     * Définit la date de publication.
     */
    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    /**
     * Retourne la date de publication sous forme de chaîne formatée.
     */
    public function getPublishedAtString(): string
    {
        return $this->publishedAt ? $this->publishedAt->format('d/m/Y') : "";
    }

    /**
     * Retourne le titre de la formation.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Définit le titre de la formation.
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Retourne la description de la formation.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Définit la description de la formation.
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Retourne l'identifiant de la vidéo associée.
     */
    public function getVideoId(): ?string
    {
        return $this->videoId;
    }

    /**
     * Définit l'identifiant de la vidéo associée.
     */
    public function setVideoId(?string $videoId): self
    {
        $this->videoId = $videoId;
        return $this;
    }

    /**
     * Retourne l'URL de la miniature YouTube.
     */
    public function getMiniature(): string
    {
        return $this->videoId ? self::CHEMIN_IMAGE . $this->videoId . "/default.jpg" : "";
    }

    /**
     * Retourne l'URL de l'image haute qualité de YouTube.
     */
    public function getPicture(): string
    {
        return $this->videoId ? self::CHEMIN_IMAGE . $this->videoId . "/hqdefault.jpg" : "";
    }

    /**
     * Retourne la playlist associée à la formation.
     */
    public function getPlaylist(): ?Playlist
    {
        return $this->playlist;
    }

    /**
     * Définit la playlist associée à la formation.
     */
    public function setPlaylist(?Playlist $playlist): self
    {
        $this->playlist = $playlist;
        return $this;
    }

    /**
     * Retourne la liste des catégories associées à la formation.
     *
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * Ajoute une catégorie à la formation.
     */
    public function addCategory(Categorie $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }
        return $this;
    }

    /**
     * Supprime une catégorie de la formation.
     */
    public function removeCategory(Categorie $category): self
    {
        $this->categories->removeElement($category);
        return $this;
    }
}
