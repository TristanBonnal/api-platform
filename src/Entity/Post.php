<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: PostRepository::class)]
// Crée automatiquement tous les endpoints et la doc pour la classe Post
// https://api-platform.com/docs/core/getting-started/#mapping-the-entities
#[ApiResource(
    normalizationContext: ['groups' => ['read:collection']],    // Permet de déterminer quel groupe de propriétés nous renvoyons dans la réponse: https://api-platform.com/docs/core/serialization/
    itemOperations: [                                           // Définit les opérations et les groupes pour les endpoints concernant un item (/{id})
        'get' => [
            'normalization_context' => ['groups' => ['read:collection', 'read:get']] 
        ],
        'put' => [
            'denormalization_context' => ['groups' => ['write:Post']]  // Denormalization détermine les champs qui sont choisis en écriture (put patch et post), 
        ],                                                             // attention : seuls les méthodes auxquelles ont été attribuées des groups seront fonctionnelles
        'delete'
    ],
    denormalizationContext: ['groups' => ['write:Post']],
)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:collection'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:collection', 'write:Post'])]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    #[
        Groups(['read:collection', 'write:Post']),
        Length(min: 5)
    ]
    private $slug;

    #[ORM\Column(type: 'text')]
    #[Groups(['read:get', 'write:Post'])]
    private $content;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:get', 'write:Post'])]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:get', 'write:Post'])]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'posts', cascade: ['persist'])]
    #[Valid()]    // Permet d'imposer les validations du sous objet (ici lenght pour Category->name)
    #[Groups(['read:get', 'write:Post'])] // Le group read:get n'est définit que dans le context itemOpérations, 
                                        // nous récupérons donc les catégories que pour un seul article demandé, et non la collection
    private $category;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
