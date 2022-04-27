<?php

namespace App\DTO\Mapper\TournamentMatch;

class TournamentMatchReusableDTOMapper
{
    public static function reusablePlayerDTO($dto, $tournamentMatch)
    {
        $dto->setMatch($tournamentMatch->getId());
        $dto->setResult($tournamentMatch->getResult());
        $dto->setWhite($tournamentMatch->getWhite()->getFullname());
        $dto->setBlack($tournamentMatch->getBlack()->getFullname());

        return $dto;
    }
}