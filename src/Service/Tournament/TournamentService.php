<?php

namespace App\Service\Tournament;

use App\Entity\Player;
use App\Entity\Tournament;
use App\Entity\TournamentCategory;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TournamentService
{


    public function canPlay($player, Tournament $tournament)
    {
        if (count($tournament->getPlayers()) >= $tournament->getMaxPlayer()) {
            return false;
        }

        if (in_array($player, $tournament->getPlayers()->toArray())) {
            return false;
        }

        if (new \DateTime() >= $tournament->getStartedAt()) {
            return false;
        }

        if (($player->getElo() < $tournament->getEloMin()) || ($player->getElo() > $tournament->getEloMax())) {
            return false;
        }

        if (empty(array_filter($tournament->getCategories(), function ($category) use ($player, $tournament) {
            // age du joueur au moment du tournoi
            $age = $this->calculateAge($player, $tournament);
            switch ($category) {
                case TournamentCategory::JUNIOR:
                    return $age >= 0 && $age < 18;
                case TournamentCategory::SENIOR:
                    return $age >= 18 && $age < 60;
                case TournamentCategory::VETERAN:
                    return $age >= 60;
                default:
                    return false;
            }
        }))) {
            return false;
        }
        return true;
    }

    public function calculateAge(Player $player, Tournament $tournament) : int {
        return date_diff($tournament->getStartedAt(), $player->getBirthday())->y;
    }


}