<?php

namespace App\DTO\Mapper\TournamentMatch;

use App\DTO\Mapper\DTOMapperInterface;
use App\DTO\Player\PlayerDTOSimple;
use App\DTO\TournamentMatch\TournamentMatchDTOSimple;
use App\Entity\Player;

class TournamentMatchSimpleDTOMapper implements DTOMapperInterface
{

    public static function transformFromObjects(iterable $objects): array
    {
        $dto = [];

        foreach ($objects as $object)
        {
            $dto[] = self::transformFromObject($object);

        }

        return $dto;
    }

    /**
     * @param $tournamentMatch
     * @return TournamentMatchDTOSimple
     */
    public static function transformFromObject($tournamentMatch): TournamentMatchDTOSimple
    {
        $dto = new TournamentMatchDTOSimple();

        TournamentMatchReusableDTOMapper::reusablePlayerDTO($dto, $tournamentMatch);

        return $dto;
    }
}