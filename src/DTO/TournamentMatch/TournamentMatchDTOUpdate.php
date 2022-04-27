<?php

namespace App\DTO\TournamentMatch;

use App\Entity\MatchResultEnum;

class TournamentMatchDTOUpdate
{
    private string $result;

    private int $matchId;


    /**
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }

    /**
     * @param string $result
     */
    public function setResult(string $result): void
    {
        $this->result = $result;
    }


    /**
     * @return int
     */
    public function getMatchId(): int
    {
        return $this->matchId;
    }

    /**
     * @param int $matchId
     */
    public function setMatchId(int $matchId): void
    {
        $this->matchId = $matchId;
    }






}