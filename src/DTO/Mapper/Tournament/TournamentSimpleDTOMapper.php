<?php

namespace App\DTO\Mapper\Tournament;

use App\DTO\Mapper\DTOMapperInterface;
use App\DTO\Tournament\TournamentDTOSimple;
use App\Entity\Tournament;

class TournamentSimpleDTOMapper implements DTOMapperInterface
{
    /**
     * @param Tournament $tournament
     * @return TournamentDTOSimple
     */
    public static function transformFromObject($tournament): TournamentDTOSimple
    {
        $dto = new TournamentDTOSimple();

        TournamentReusableDTOMapper::reusableTournamentDTO($dto, $tournament);

        return $dto;
    }

}