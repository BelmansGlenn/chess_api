<?php

namespace App\DTO\PlayerScore;

class PlayerScoreDTOSimple
{
    private int $id;

    private string $firstname;

    private string $lastname;

    private string $image;

    private int $victories;

    private int $ties;

    private int $defeats;

    private float $score;

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
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return int
     */
    public function getVictories(): int
    {
        return $this->victories;
    }

    /**
     * @param int $victories
     */
    public function setVictories(int $victories): void
    {
        $this->victories = $victories;
    }

    /**
     * @return int
     */
    public function getTies(): int
    {
        return $this->ties;
    }

    /**
     * @param int $ties
     */
    public function setTies(int $ties): void
    {
        $this->ties = $ties;
    }

    /**
     * @return int
     */
    public function getDefeats(): int
    {
        return $this->defeats;
    }

    /**
     * @param int $defeats
     */
    public function setDefeats(int $defeats): void
    {
        $this->defeats = $defeats;
    }

    /**
     * @return float
     */
    public function getScore(): float
    {
        return $this->score;
    }

    /**
     * @param float $score
     */
    public function setScore(float $score): void
    {
        $this->score = $score;
    }


}