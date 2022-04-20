<?php

namespace App\Entity;

use App\Repository\TournamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TournamentRepository::class)]
class Tournament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 60)]
    private $name;

    #[ORM\Column(type: 'datetime')]
    #[Assert\GreaterThan('today')]
    private $startedAt;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\LessThan(propertyPath: 'eloMax', message: 'This number must be less than the Elo Max')]
    private $eloMin = 0;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\GreaterThan(propertyPath: 'eloMin', message: 'This number must be greater than the Elo Min')]
    private $eloMax = 9999;

    #[Assert\Count(min: 1)]
    #[ORM\Column(type: 'array')]
    private $categories = [];

    #[ORM\Column(type: 'array')]
    #[Assert\Count(min: 1)]
    private $gender = [];

    #[ORM\Column(type: 'integer')]
    #[Assert\DivisibleBy(2, message: 'The number must be an even number')]
    #[Assert\Range(min: 2, max: 100)]
    private $maxPlayer;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $currentRound = 0;

    #[ORM\ManyToMany(targetEntity: Player::class, inversedBy: 'tournaments')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private $players;

    #[ORM\Column(type: 'boolean')]
    private $isFinished = false;

    public function __construct()
    {
        $this->players = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getEloMin(): ?int
    {
        return $this->eloMin;
    }

    public function setEloMin(?int $eloMin): self
    {
        $this->eloMin = $eloMin;

        return $this;
    }

    public function getEloMax(): ?int
    {
        return $this->eloMax;
    }

    public function setEloMax(?int $eloMax): self
    {
        $this->eloMax = $eloMax;

        return $this;
    }

    public function getCategories(): array
    {
        return array_map(function($item){
            return TournamentCategory::from($item);
    }, $this->categories);
    }

    public function setCategories(array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function getGender(): array
    {
        return array_map(function ($item){
            return GenderEnum::from($item);
        }, $this->gender);
    }

    public function setGender(array $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getMaxPlayer(): ?int
    {
        return $this->maxPlayer;
    }

    public function setMaxPlayer(int $maxPlayer): self
    {
        $this->maxPlayer = $maxPlayer;

        return $this;
    }

    public function getCurrentRound(): ?int
    {
        return $this->currentRound;
    }

    public function setCurrentRound(int $currentRound): self
    {
        $this->currentRound = $currentRound;

        return $this;
    }

    /**
     * @return Collection<int, Player>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
        }

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        $this->players->removeElement($player);

        return $this;
    }

    public function getIsFinished(): ?bool
    {
        return $this->isFinished;
    }

    public function setIsFinished(bool $isFinished): self
    {
        $this->isFinished = $isFinished;

        return $this;
    }
}
