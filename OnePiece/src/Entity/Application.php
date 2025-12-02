<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomAppli = null;

    #[ORM\Column(length: 255)]
    private ?string $URL = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    /**
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'applications')]
    private Collection $appliRole;

    public function __construct()
    {
        $this->appliRole = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomAppli(): ?string
    {
        return $this->nomAppli;
    }

    public function setNomAppli(string $nomAppli): static
    {
        $this->nomAppli = $nomAppli;

        return $this;
    }

    public function getURL(): ?string
    {
        return $this->URL;
    }

    public function setURL(string $URL): static
    {
        $this->URL = $URL;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getAppliRole(): Collection
    {
        return $this->appliRole;
    }

    public function addAppliRole(Role $appliRole): static
    {
        if (!$this->appliRole->contains($appliRole)) {
            $this->appliRole->add($appliRole);
        }

        return $this;
    }

    public function removeAppliRole(Role $appliRole): static
    {
        $this->appliRole->removeElement($appliRole);

        return $this;
    }
}
