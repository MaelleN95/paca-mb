<?php

namespace App\Entity;

use App\Repository\ToolTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ToolTypeRepository::class)]
class ToolType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom du type d’outil ne peut pas être vide.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom du type d’outil ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'toolType', targetEntity: Tool::class)]
    private Collection $tools;

    public function __construct()
    {
        $this->tools = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getTools(): Collection
    {
        return $this->tools;
    }

    public function addTool(Tool $tool): static
    {
        if (!$this->tools->contains($tool)) {
            $this->tools->add($tool);
            $tool->setToolType($this);
        }
        return $this;
    }

    public function removeTool(Tool $tool): static
    {
        if ($this->tools->removeElement($tool)) {
            if ($tool->getToolType() === $this) {
                $tool->setToolType(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? 'Type d’outil';
    }
}
