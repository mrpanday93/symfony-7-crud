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
    public ?int $row_number = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

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

    public function getRowNumber(): ?int
    {
        return $this->row_number;
    }

    public function setRowNumber(int $row_number): static
    {
        $this->row_number = $row_number;

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
