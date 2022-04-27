<?php

namespace App\DTO\TournamentMatch;

use App\Entity\MatchResultEnum;

class TournamentMatchDTOSimple
{
    private int $match;

    private MatchResultEnum $result;

    private string $white;

    private string $black;

    /**
     * @return int
     */
    public function getMatch(): int
    {
        return $this->match;
    }

    /**
     * @param int $match
     */
    public function setMatch(int $match): void
    {
        $this->match = $match;
    }


    /**
     * @return MatchResultEnum
     */
    public function getResult(): MatchResultEnum
    {
        return $this->result;
    }

    /**
     * @param MatchResultEnum $result
     */
    public function setResult(MatchResultEnum $result): void
    {
        $this->result = $result;
    }


    /**
     * @return string
     */
    public function getWhite(): string
    {
        return $this->white;
    }

    /**
     * @param string $white
     */
    public function setWhite(string $white): void
    {
        $this->white = $white;
    }

    /**
     * @return string
     */
    public function getBlack(): string
    {
        return $this->black;
    }

    /**
     * @param string $black
     */
    public function setBlack(string $black): void
    {
        $this->black = $black;
    }


}