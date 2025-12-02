<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UtilisateurRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: '`utilisateur`')]
#[UniqueEntity(fields: ['PieceId'], message: 'Cette pièce est déjà utilisée.')]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Nationalite = null;

    #[ORM\Column(length: 255)]
    private ?string $PieceId = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $FaitLe = null;

    #[ORM\Column(length: 255)]
    private ?string $FaitA = null;

    #[ORM\Column]
    private ?bool $Dupli = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $DateDupli = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $LieuDupli = null;

    #[ORM\Column(length: 255)]
    private ?string $Recto = null;

    #[ORM\Column(length: 255)]
    private ?string $Verso = null;

    #[ORM\Column(length: 255)]
    private ?string $civilite = null;

    #[ORM\Column(length: 255)]
    private ?string $Nom = null;

    #[ORM\Column(length: 255)]
    private ?string $Prenoms = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $DateNaissance = null;

    #[ORM\Column(length: 255)]
    private ?string $Domicile = null;

    #[ORM\Column(length: 255)]
    private ?string $NumTel = null;

    #[ORM\Column(length: 255)]
    private ?string $Email = null;

    #[ORM\Column(length: 255)]
    private ?string $Ville = null;

    #[ORM\Column(length: 255)]
    private ?string $District = null;

    #[ORM\Column(length: 255)]
    private ?string $Region = null;

    #[ORM\Column(length: 255)]
    private ?string $commune = null;

    #[ORM\Column(length: 255)]
    private ?string $MotDePasse = null;

    /**
     * @var Collection<int, DemanderRole>
     */
    #[ORM\OneToMany(targetEntity: DemanderRole::class, mappedBy: 'Utilisateur', orphanRemoval: true)]
    private Collection $demanderRoles;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $photo = null;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'relation')]
    private Collection $messages;

    #[ORM\Column(nullable: true)]
    private ?string $resetCode = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $resetCodeExpiresAt = null;


    public function __construct()
    {
        $this->demanderRoles = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNationalite(): ?string
    {
        return $this->Nationalite;
    }

    public function setNationalite(string $Nationalite): static
    {
        $this->Nationalite = $Nationalite;
        return $this;
    }

    public function getPieceId(): ?string
    {
        return $this->PieceId;
    }

    public function setPieceId(string $PieceId): static
    {
        $this->PieceId = $PieceId;
        return $this;
    }

    public function getFaitLe(): ?\DateTime
    {
        return $this->FaitLe;
    }

    public function setFaitLe(\DateTime $FaitLe): static
    {
        $this->FaitLe = $FaitLe;
        return $this;
    }

    public function getFaitA(): ?string
    {
        return $this->FaitA;
    }

    public function setFaitA(string $FaitA): static
    {
        $this->FaitA = $FaitA;
        return $this;
    }

    public function isDupli(): ?bool
    {
        return $this->Dupli;
    }

    public function setDupli(bool $Dupli): static
    {
        $this->Dupli = $Dupli;
        return $this;
    }

    public function getDateDupli(): ?\DateTime
    {
        return $this->DateDupli;
    }

    public function setDateDupli(?\DateTime $DateDupli): static
    {
        $this->DateDupli = $DateDupli;
        return $this;
    }

    public function getLieuDupli(): ?string
    {
        return $this->LieuDupli;
    }

    public function setLieuDupli(?string $LieuDupli): static
    {
        $this->LieuDupli = $LieuDupli;
        return $this;
    }

    public function getRecto(): ?string
    {
        return $this->Recto;
    }

    public function setRecto(string $Recto): static
    {
        $this->Recto = $Recto;
        return $this;
    }

    public function getVerso(): ?string
    {
        return $this->Verso;
    }

    public function setVerso(string $Verso): static
    {
        $this->Verso = $Verso;
        return $this;
    }

    public function getCivilite(): ?string
    {
        return $this->civilite;
    }

    public function setCivilite(string $civilite): static
    {
        $this->civilite = $civilite;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;
        return $this;
    }

    public function getPrenoms(): ?string
    {
        return $this->Prenoms;
    }

    public function setPrenoms(string $Prenoms): static
    {
        $this->Prenoms = $Prenoms;
        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->DateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $DateNaissance): static
    {
        $this->DateNaissance = $DateNaissance;
        return $this;
    }

    public function getDomicile(): ?string
    {
        return $this->Domicile;
    }

    public function setDomicile(string $Domicile): static
    {
        $this->Domicile = $Domicile;
        return $this;
    }

    public function getNumTel(): ?string
    {
        return $this->NumTel;
    }

    public function setNumTel(string $NumTel): static
    {
        $this->NumTel = $NumTel;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): static
    {
        $this->Email = $Email;
        return $this;
    }

    public function getVille(): ?string
    {
        return $this->Ville;
    }

    public function setVille(string $Ville): static
    {
        $this->Ville = $Ville;
        return $this;
    }

    public function getDistrict(): ?string
    {
        return $this->District;
    }

    public function setDistrict(string $District): static
    {
        $this->District = $District;
        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->Region;
    }

    public function setRegion(string $Region): static
    {
        $this->Region = $Region;
        return $this;
    }

    public function getCommune(): ?string
    {
        return $this->commune;
    }

    public function setCommune(string $commune): static
    {
        $this->commune = $commune;
        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->MotDePasse;
    }

    public function setMotDePasse(string $MotDePasse): static
    {
        $this->MotDePasse = $MotDePasse;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->PieceId;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->MotDePasse;
    }

    public function setPassword(string $password): static
    {
        $this->MotDePasse = $password;
        return $this;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // Si tu stockes des données sensibles temporaires sur l'utilisateur, efface-les ici
        // Par exemple : $this->plainPassword = null;
    }

    /**
     * @return Collection<int, DemanderRole>
     */
    public function getDemanderRoles(): Collection
    {
        return $this->demanderRoles;
    }

    public function addDemanderRole(DemanderRole $demanderRole)
    {
        if (!$this->demanderRoles->contains($demanderRole)) {
            $this->demanderRoles->add($demanderRole);
        }

    }
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
}

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setRelation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getRelation() === $this) {
                $message->setRelation(null);
            }
        }

        return $this;
    }
    public function getResetCode(): ?string
    {
        return $this->resetCode;
    }

    public function setResetCode(?string $resetCode): static
    {
        $this->resetCode = $resetCode;
        return $this;
    }

    public function getResetCodeExpiresAt(): ?\DateTimeImmutable
    {
        return $this->resetCodeExpiresAt;
    }

    public function setResetCodeExpiresAt(?\DateTimeImmutable $date): static
    {
        $this->resetCodeExpiresAt = $date;
        return $this;
    }
    

}