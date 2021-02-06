<?php

namespace App\Entity;

use App\Repository\EtudiantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EtudiantRepository::class)
 */
class Etudiant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    private $firstname;

    private $lastname;

    private $sexe;

    private $dateNaiss;

    private $email;

    private $phone;

    private $hashPassword;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="etudiant", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Filiere::class, inversedBy="etudiants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $filiere;

    /**
     * @ORM\ManyToOne(targetEntity=Classe::class, inversedBy="etudiants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $classe;

    /**
     * @ORM\ManyToOne(targetEntity=Club::class, inversedBy="etudiants")
     */
    private $club;

    /**
     * @ORM\OneToOne(targetEntity=Membership::class, mappedBy="etudiant", cascade={"persist", "remove"})
     */
    private $membership;

    /**
     * @ORM\OneToOne(targetEntity=President::class, mappedBy="etudiant", cascade={"persist", "remove"})
     */
    private $president;

    /**
     * @ORM\OneToMany(targetEntity=Participation::class, mappedBy="etudiant", orphanRemoval=true)
     */
    private $participations;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
    }


    
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getDateNaiss(): ?\DateTimeInterface
    {
        return $this->dateNaiss;
    }

    public function setDateNaiss(\DateTimeInterface $dateNaiss): self
    {
        $this->dateNaiss = $dateNaiss;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getHashPassword(): ?string
    {
        return $this->hashPassword;
    }

    public function setHashPassword(string $hashPassword): self
    {
        $this->hashPassword = $hashPassword;

        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFiliere(): ?Filiere
    {
        return $this->filiere;
    }

    public function setFiliere(?Filiere $filiere): self
    {
        $this->filiere = $filiere;

        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function setClub(?Club $club): self
    {
        $this->club = $club;

        return $this;
    }

    public function getMembership(): ?Membership
    {
        return $this->membership;
    }

    public function setMembership(Membership $membership): self
    {
        // set the owning side of the relation if necessary
        if ($membership->getEtudiant() !== $this) {
            $membership->setEtudiant($this);
        }

        $this->membership = $membership;

        return $this;
    }

    public function getPresident(): ?President
    {
        return $this->president;
    }

    public function setPresident(President $president): self
    {
        // set the owning side of the relation if necessary
        if ($president->getEtudiant() !== $this) {
            $president->setEtudiant($this);
        }

        $this->president = $president;

        return $this;
    }

    /**
     * @return Collection|Participation[]
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): self
    {
        if (!$this->participations->contains($participation)) {
            $this->participations[] = $participation;
            $participation->setEtudiant($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->participations->removeElement($participation)) {
            // set the owning side to null (unless already changed)
            if ($participation->getEtudiant() === $this) {
                $participation->setEtudiant(null);
            }
        }

        return $this;
    }
}
