<?php

namespace App\DTO\Mapper\PlayerScore;

use App\DTO\Mapper\DTOMapperInterface;
use App\DTO\PlayerScore\PlayerScoreDTOSimple;
use App\Entity\PlayerScore;


class PlayerScoreSimpleDTOMapper implements DTOMapperInterface
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
     * @param PlayerScore $playerScore
     * @return PlayerScoreDTOSimple
     */
    public static function transformFromObject($playerScore): PlayerScoreDTOSimple
    {
        $dto = new PlayerScoreDTOSimple();

        PlayerScoreReusableDTOMapper::reusablePlayerDTO($dto, $playerScore);

        return $dto;
    }
}