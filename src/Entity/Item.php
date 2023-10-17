<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $bookID;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $title;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $authors;

    #[ORM\Column(type: 'float', nullable: false)]
    private float $averageRating;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $isbn;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $isbn13;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $languageCode;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $numPages;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $ratingsCount;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $textReviewsCount;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $publicationDate;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $publisher;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookID(): int
    {
        return $this->bookID;
    }

    public function setBookID(int $bookID): self
    {
        $this->bookID = $bookID;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getAuthors(): string
    {
        return $this->authors;
    }

    public function setAuthors(string $authors): self
    {
        $this->authors = $authors;
        return $this;
    }

    public function getAverageRating(): float
    {
        return $this->averageRating;
    }

    public function setAverageRating(float $averageRating): self
    {
        $this->averageRating = $averageRating;
        return $this;
    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;
        return $this;
    }

    public function getIsbn13(): string
    {
        return $this->isbn13;
    }

    public function setIsbn13(string $isbn13): self
    {
        $this->isbn13 = $isbn13;
        return $this;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    public function setLanguageCode(string $languageCode): self
    {
        $this->languageCode = $languageCode;
        return $this;
    }

    public function getNumPages(): int
    {
        return $this->numPages;
    }

    public function setNumPages(int $numPages): self
    {
        $this->numPages = $numPages;
        return $this;
    }

    public function getRatingsCount(): int
    {
        return $this->ratingsCount;
    }

    public function setRatingsCount(int $ratingsCount): self
    {
        $this->ratingsCount = $ratingsCount;
        return $this;
    }

    public function getTextReviewsCount(): int
    {
        return $this->textReviewsCount;
    }

    public function setTextReviewsCount(int $textReviewsCount): self
    {
        $this->textReviewsCount = $textReviewsCount;
        return $this;
    }

    public function getPublicationDate(): string
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(string $publicationDate): self
    {
        $this->publicationDate = $publicationDate;
        return $this;
    }

    public function getPublisher(): string
    {
        return $this->publisher;
    }

    public function setPublisher(string $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }
}
