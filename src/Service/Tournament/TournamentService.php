<?php

namespace App\Service\Tournament;

use App\Entity\Player;
use App\Entity\Tournament;
use App\Entity\TournamentCategory;
use App\Exception\RulesTournamentException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TournamentService
{


    public function canPlay($player, Tournament $tournament)
    {

        if (count($tournament->getPlayers()) >= $tournament->getMaxPlayer()) {
            throw new RulesTournamentException('The tournament is already full.');
        }

        if (in_array($player, $tournament->getPlayers()->toArray())) {
            throw new RulesTournamentException('You are already registered.');
        }

        if (new \DateTime() >= $tournament->getStartedAt()) {
            throw new RulesTournamentException('The tournament has already started.');
        }

        if (($player->getElo() < $tournament->getEloMin()) || ($player->getElo() > $tournament->getEloMax())) {
            throw new RulesTournamentException('Your elo doesn\'t match the elo requested.');
        }
        $hasRightGender = false;
        foreach ($tournament->getGender() as $gender) {
            $gender = get_object_vars($gender);
            if (in_array($player->getGender(), $gender) == true)
            {
                $hasRightGender = true;
            }
        }
        if ($hasRightGender === false) {
            throw new RulesTournamentException('You don\'t have the right gender to participate at this tournament');
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
                    throw new RulesTournamentException('This category doesn\'t exist.');
            }
        }))) {
            throw new RulesTournamentException('You don\'t have the right age to be part of this category.');
        }
        return true;
    }

    public function calculateAge(Player $player, Tournament $tournament) : int {
        return date_diff($tournament->getStartedAt(), $player->getBirthday())->y;
    }


}