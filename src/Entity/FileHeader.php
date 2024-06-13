<?php

namespace App\Entity;

use App\Repository\FileHeaderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FileHeaderRepository::class)]
#[ORM\HasLifecycleCallbacks]
class FileHeader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255)]
    public ?string $title = null;

    #[ORM\Column]
    public ?int $file_id = null;

    #[ORM\Column]
    public ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    public ?int $column_index = null;

    #[ORM\PrePersist]
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
    
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

    public function getFileId(): ?int
    {
        return $this->file_id;
    }

    public function setFileId(int $file_id): static
    {
        $this->file_id = $file_id;

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

    public function getColumnIndex(): ?int
    {
        return $this->column_index;
    }

    public function setColumnIndex(int $column_index): static
    {
        $this->column_index = $column_index;

        return $this;
    }
}
