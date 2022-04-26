<?php

namespace App\Entity;

use App\Repository\PlayerScoreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerScoreRepository::class)]
class PlayerScore
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $lastname;

    #[ORM\Column(type: 'string', length: 255)]
    private $firstname;

    #[ORM\Column(type: 'string', length: 255)]
    private $image;

    #[ORM\Column(type: 'integer')]
    private $victories;

    #[ORM\Column(type: 'integer')]
    private $defeats;

    #[ORM\Column(type: 'integer')]
    private $ties;

    #[ORM\Column(type: 'float')]
    private $score;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getVictories(): ?int
    {
        return $this->victories;
    }

    public function setVictories(int $victories): self
    {
        $this->victories = $victories;

        return $this;
    }

    public function getDefeats(): ?int
    {
        return $this->defeats;
    }

    public function setDefeats(int $defeats): self
    {
        $this->defeats = $defeats;

        return $this;
    }

    public function getTies(): ?int
    {
        return $this->ties;
    }

    public function setTies(int $ties): self
    {
        $this->ties = $ties;

        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(float $score): self
    {
        $this->score = $score;

        return $this;
    }
}
