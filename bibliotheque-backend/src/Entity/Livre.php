<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\LivreRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LivreRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['livre:read']],
    denormalizationContext: ['groups' => ['livre:write']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            security: "is_granted('ROLE_USER')",
            processor: \App\State\LivreProcessor::class
        ),
        new Put(
            security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getProprietaire() == user)",
            processor: \App\State\LivreProcessor::class
        ),
        new Delete(security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getProprietaire() == user)")
    ]
)]
class Livre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['livre:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['livre:read', 'livre:write'])]
    private ?string $titre = null;

    #[ORM\Column(length: 13, unique: true, nullable: true)]
    #[Groups(['livre:read', 'livre:write'])]
    private ?string $isbn = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['livre:read', 'livre:write'])]
    private ?string $resume = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['livre:read', 'livre:write'])]
    private ?\DateTimeInterface $datePublication = null;

    #[ORM\Column]
    #[Groups(['livre:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['livre:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Auteur::class, inversedBy: 'livres')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['livre:read', 'livre:write'])]
    private ?Auteur $auteur = null;

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'livres')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['livre:read', 'livre:write'])]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'livres')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['livre:read', 'livre:write'])]
    private ?User $proprietaire = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): static
    {
        $this->isbn = $isbn;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(?string $resume): static
    {
        $this->resume = $resume;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->datePublication;
    }

    public function setDatePublication(?\DateTimeInterface $datePublication): static
    {
        $this->datePublication = $datePublication;
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

    public function getAuteur(): ?Auteur
    {
        return $this->auteur;
    }

    public function setAuteur(?Auteur $auteur): static
    {
        $this->auteur = $auteur;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getProprietaire(): ?User
    {
        return $this->proprietaire;
    }

    public function setProprietaire(?User $proprietaire): static
    {
        $this->proprietaire = $proprietaire;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }
}
