<?php

namespace App\DTO\Mapper\Tournament;

class TournamentReusableDTOMapper
{
    public static function reusableTournamentDTO($dto, $tournament)
    {
        $dto->setId($tournament->getId());
        $dto->setName($tournament->getName());
        $dto->setStartedAt($tournament->getStartedAt());
        $dto->setEloMin($tournament->getEloMin());
        $dto->setEloMax($tournament->getEloMax());
        $dto->setCategories($tournament->getCategories());
        $dto->setGender($tournament->getGender());
        $dto->setMaxPlayer($tournament->getMaxPlayer());

        return $dto;
    }
}