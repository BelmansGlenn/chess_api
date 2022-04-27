<?php

namespace App\Entity;

use App\Repository\TournamentMatchRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TournamentMatchRepository::class)]
class TournamentMatch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $round;

    #[ORM\Column(type: 'string',  enumType: MatchResultEnum::class)]
    private $result;

    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $white;

    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $black;

    #[ORM\ManyToOne(targetEntity: Tournament::class, inversedBy: 'tournamentMatches')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private $tournament;

    public function getId(): ?int
    {
        return $this->id;
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


    public function getResult(): MatchResultEnum
    {
        return $this->result;
    }


    public function setResult(MatchResultEnum $result): self
    {
        $this->result = $result;

        return $this;
    }



    public function getWhite(): ?Player
    {
        return $this->white;
    }

    public function setWhite(?Player $white): self
    {
        $this->white = $white;

        return $this;
    }

    public function getBlack(): ?Player
    {
        return $this->black;
    }

    public function setBlack(?Player $black): self
    {
        $this->black = $black;

        return $this;
    }

    public function getTournament(): ?Tournament
    {
        return $this->tournament;
    }

    public function setTournament(?Tournament $tournament): self
    {
        $this->tournament = $tournament;

        return $this;
    }

}
