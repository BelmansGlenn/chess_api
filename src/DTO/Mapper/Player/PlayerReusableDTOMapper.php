<?php

namespace App\DTO\Mapper\Player;

class PlayerReusableDTOMapper
{
    public static function reusablePlayerDTO($dto, $player)
    {
        $dto->setId($player->getId());
        $dto->setEmail($player->getEmail());
        $dto->setFirstname($player->getFirstname());
        $dto->setLastname($player->getLastname());
        $dto->setBirthday($player->getBirthday());
        $dto->setGender($player->getGender());
        $dto->setElo($player->getElo());
        $dto->setImage($player->getImage());

        return $dto;
    }
}