<?php

namespace App\DTO\Mapper\PlayerScore;

class PlayerScoreReusableDTOMapper
{
    public static function reusablePlayerDTO($dto, $playerScore)
    {
        $dto->setId($playerScore->getId());
        $dto->setFirstname($playerScore->getPlayer()->getFirstname());
        $dto->setLastname($playerScore->getPlayer()->getLastname());
        $dto->setImage($playerScore->getPlayer()->getImage());
        $dto->setVictories($playerScore->getVictories());
        $dto->setTies($playerScore->getTies());
        $dto->setDefeats($playerScore->getDefeats());
        $dto->setScore($playerScore->getScore());

        return $dto;
    }
}