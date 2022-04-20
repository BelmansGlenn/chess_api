<?php

namespace App\DTO\Mapper\Player;

use App\DTO\Mapper\DTOMapperInterface;
use App\DTO\Player\PlayerDTOSimple;
use App\Entity\Player;

class PlayerSimpleDTOMapper implements DTOMapperInterface
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
     * @param Player $player
     * @return PlayerDTOSimple
     */
    public static function transformFromObject($player): PlayerDTOSimple
    {
        $dto = new PlayerDTOSimple();

        PlayerReusableDTOMapper::reusablePlayerDTO($dto, $player);

        return $dto;
    }
}