<?php

namespace App\Entity;

use App\Repository\PresidentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PresidentRepository::class)
 */
class President
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Club::class, inversedBy="president", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $club;

    /**
     * @ORM\OneToOne(targetEntity=Etudiant::class, inversedBy="president", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $etudiant;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function setClub(Club $club): self
    {
        $this->club = $club;

        return $this;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(Etudiant $etudiant): self
    {
        $this->etudiant = $etudiant;

        return $this;
    }
}
