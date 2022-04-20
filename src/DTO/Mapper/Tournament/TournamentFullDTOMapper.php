<?php

namespace App\DTO\Mapper\Tournament;

use App\DTO\Mapper\DTOMapperInterface;
use App\DTO\Mapper\Player\PlayerSimpleDTOMapper;
use App\DTO\Tournament\TournamentDTOFull;
use App\Entity\Tournament;
use App\Service\Pagination\PaginationService;

class TournamentFullDTOMapper implements DTOMapperInterface
{

    /**
     * @param Tournament $tournament
     * @return TournamentDTOFull
     */
    public static function transformFromObject($tournament): TournamentDTOFull
    {
        $dto = new TournamentDTOFull();

        TournamentReusableDTOMapper::reusableTournamentDTO($dto, $tournament);

        $dto->setPlayers(PlayerSimpleDTOMapper::transformFromObjects($tournament->getPlayers()->toArray()));

        return $dto;
    }
}