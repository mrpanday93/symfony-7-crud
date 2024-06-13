<?php

namespace App\Entity;

use App\Repository\FileValuesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FileValuesRepository::class)]
#[ORM\HasLifecycleCallbacks]
class FileValues
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $header_id = null;

    #[ORM\Column(length: 255)]
    public ?string $value = null;

    #[ORM\Column]
    private ?int $file_row_index = null;

    #[ORM\Column]
    public ?\DateTimeImmutable $createdAt = null;
    #[ORM\PrePersist]
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHeaderId(): ?int
    {
        return $this->header_id;
    }

    public function setHeaderId(int $header_id): static
    {
        $this->header_id = $header_id;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    
    public function getFileRowIndex(): ?int
    {
        return $this->file_row_index;
    }

    public function setFileRowIndex(int $file_row_index): static
    {
        $this->file_row_index = $file_row_index;

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
}
