<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),  // Lecture publique
        new Get(),           // Lecture publique
        new Post(security: "is_granted('ROLE_USER')"),  // Création par utilisateurs authentifiés
        new Put(security: "is_granted('ROLE_ADMIN')"),  // Modification par administrateurs uniquement
        new Delete(security: "is_granted('ROLE_ADMIN')") // Suppression par administrateurs uniquement
    ],
    filters: ['App\Filter\SearchFilter']
)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['livre:read', 'categorie:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Groups(['livre:read', 'categorie:read', 'categorie:write'])]
    private ?string $nom = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 7, nullable: true)]
    private ?string $couleur = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Livre>
     */
    #[ORM\OneToMany(targetEntity: Livre::class, mappedBy: 'categorie')]
    private Collection $livres;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->livres = new ArrayCollection();
        $this->couleur = $this->generateRandomColor();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): static
    {
        $this->couleur = $couleur;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Livre>
     */
    public function getLivres(): Collection
    {
        return $this->livres;
    }

    public function addLivre(Livre $livre): static
    {
        if (!$this->livres->contains($livre)) {
            $this->livres->add($livre);
            $livre->setCategorie($this);
        }

        return $this;
    }

    public function removeLivre(Livre $livre): static
    {
        if ($this->livres->removeElement($livre)) {
            if ($livre->getCategorie() === $this) {
                $livre->setCategorie(null);
            }
        }

        return $this;
    }

    /**
     * Génère une couleur hexadécimale aléatoire
     */
    private function generateRandomColor(): string
    {
        $colors = [
            '#6C5CE7', // Violet
            '#0984E3', // Bleu
            '#00B894', // Vert
            '#E17055', // Orange
            '#D63031', // Rouge
            '#E84393', // Rose
            '#FDCB6E', // Jaune
            '#00CEC9', // Teal
            '#4C51BF', // Indigo
            '#636E72', // Gris
            '#2D3436', // Noir
            '#A0522D', // Marron
            '#6C5CE7', // Violet foncé
            '#74B9FF', // Bleu clair
            '#55A3FF', // Bleu ciel
            '#FD79A8', // Rose clair
            '#FDCB6E', // Jaune doré
            '#E17055', // Orange vif
            '#00B894', // Vert émeraude
            '#6C5CE7'  // Violet royal
        ];

        return $colors[array_rand($colors)];
    }
}
