<?php

namespace App\DTO\Tournament;

use App\DTO\Paginator\PaginatorDTO;
use App\DTO\Player\PlayerDTOSimple;

class TournamentDTOFull
{

    private int $id;

    private string $name;

    private \DateTimeInterface $startedAt;

    private int $eloMin;

    private int $eloMax;

    private array $categories = [];

    private array $gender = [];

    private int $maxPlayer;

    private array $players;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getStartedAt(): \DateTimeInterface
    {
        return $this->startedAt;
    }

    /**
     * @param \DateTimeInterface $startedAt
     */
    public function setStartedAt(\DateTimeInterface $startedAt): void
    {
        $this->startedAt = $startedAt;
    }

    /**
     * @return int
     */
    public function getEloMin(): int
    {
        return $this->eloMin;
    }

    /**
     * @param int $eloMin
     */
    public function setEloMin(int $eloMin): void
    {
        $this->eloMin = $eloMin;
    }

    /**
     * @return int
     */
    public function getEloMax(): int
    {
        return $this->eloMax;
    }

    /**
     * @param int $eloMax
     */
    public function setEloMax(int $eloMax): void
    {
        $this->eloMax = $eloMax;
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     */
    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }

    /**
     * @return array
     */
    public function getGender(): array
    {
        return $this->gender;
    }

    /**
     * @param array $gender
     */
    public function setGender(array $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return int
     */
    public function getMaxPlayer(): int
    {
        return $this->maxPlayer;
    }

    /**
     * @param int $maxPlayer
     */
    public function setMaxPlayer(int $maxPlayer): void
    {
        $this->maxPlayer = $maxPlayer;
    }

    /**
     * @return array
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * @param array $players
     */
    public function setPlayers(array $players): void
    {
        $this->players = $players;
    }







}