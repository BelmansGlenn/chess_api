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

    #[ORM\Column(type: 'integer')]
    private $victories;

    #[ORM\Column(type: 'integer')]
    private $defeats;

    #[ORM\Column(type: 'integer')]
    private $ties;

    #[ORM\Column(type: 'float')]
    private $score;

    #[ORM\Column(type: 'integer')]
    private $round;


    #[ORM\Column(type: 'integer')]
    private $TournamentId;

    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $player;


    public function getId(): ?int
    {
        return $this->id;
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


    public function getRound(): ?int
    {
        return $this->round;
    }

    public function setRound(int $round): self
    {
        $this->round = $round;

        return $this;
    }


    public function getTournamentId(): ?int
    {
        return $this->TournamentId;
    }

    public function setTournamentId(int $TournamentId): self
    {
        $this->TournamentId = $TournamentId;

        return $this;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        return $this;
    }
}
