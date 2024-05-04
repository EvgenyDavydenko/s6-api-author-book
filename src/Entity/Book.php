<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    // with "groups()" I limit the data to return
    #[Groups(['getBooks', 'getAuthors'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    // with "groups()" I limit the data to return
    #[Groups(['getBooks', 'getAuthors'])]
    #[Assert\NotBlank(message:"the title of the book is required")]
    #[Assert\Length(min:4,max:40,minMessage:'your title must have at least {{ limit }} words ',maxMessage:'your title cannot have more than {{limit}} words')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    // with "groups()" I limit the data to return
    #[Groups(['getBooks', 'getAuthors'])]
    private ?string $coverText = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    // with "groups()" I limit the data to return
    #[Groups('getBooks')]
    // It will allow me to avoid problems when deleting my author
    // to be used when I try to delete an element from another entity that is linked to this entity
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private ?Author $author = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCoverText(): ?string
    {
        return $this->coverText;
    }

    public function setCoverText(?string $coverText): static
    {
        $this->coverText = $coverText;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): static
    {
        $this->author = $author;

        return $this;
    }
}