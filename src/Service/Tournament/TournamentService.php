<?php

namespace App\Service\Tournament;

use App\Entity\MatchResult;
use App\Entity\MatchResultEnum;
use App\Entity\Player;
use App\Entity\PlayerScore;
use App\Entity\Tournament;
use App\Entity\TournamentCategory;
use App\Entity\TournamentMatch;
use App\Exception\CustomBadRequestException;
use App\Exception\RouteNotFoundException;
use App\Exception\RulesTournamentException;
use App\Repository\PlayerScoreRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TournamentService
{

    private PlayerScoreRepository $playerScoreRepository;

    /**
     * @param PlayerScoreRepository $playerScoreRepository
     */
    public function __construct(PlayerScoreRepository $playerScoreRepository)
    {
        $this->playerScoreRepository = $playerScoreRepository;
    }


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


    public function canStart(Tournament $tournament) : bool
    {

        if($tournament->getCurrentRound() > 0) {
            throw new RulesTournamentException('The tournament already started.');
        }
        if (Count($tournament->getPlayers()) % 2 != 0)
        {
            throw new RulesTournamentException('A tournament can\'t be start if there isn\'t an even number of players that participate.');
        }
//        if ((new \DateTime()) < $tournament->getStartedAt())
//        {
//            throw new RulesTournamentException('A tournament cannot begin before the start date.');
//        }
        return true;
    }

    public function createTournamentMatches($players, $tournament, $count)
    {


        for ($r = 1; $r < $count; $r++) {
            for ($i = 0; $i < $count / 2; $i++) {
                $m = new TournamentMatch();
                $m->setWhite($players[$i]);
                $m->setBlack($players[$count - 1 - $i]);
                $m->setRound($r);
                $m->setTournament($tournament);
                $m->setResult(MatchResultEnum::NOT_PLAYED);
                yield $m;
            }
            $players = array_merge(
                [$players[0], $players[$count-1]],
                array_slice($players, 1, $count-2)
            );
        }
    }

    public function canUpdate($result, Tournament $tournament, TournamentMatch $tournamentMatch)
    {
        if ($tournament->getCurrentRound() != $tournamentMatch->getRound())
        {
            throw new RouteNotFoundException();
        }
        $tournamentMatch->setResult($result);
        return $tournamentMatch;
    }

    public function scoreFirstRound($tournament,$tournamentMatchCurrentRound, $tournamentMatchPreviousRound)
    {
        if ($tournamentMatchPreviousRound == null)
        {
            foreach ($tournamentMatchCurrentRound as $currentRound)
            {
                $playerScore1 = new PlayerScore();
                $playerScore2 = new PlayerScore();
                $playerScore1->setPlayer($currentRound->getWhite());
                $playerScore2->setPlayer($currentRound->getBlack());
                $playerScore1->setTournamentId($tournament->getId());
                $playerScore2->setTournamentId($tournament->getId());
                $playerScore1->setRound(1);
                $playerScore2->setRound(1);
                switch ($currentRound->getResult())
                {

                    case MatchResultEnum::WHITE_WIN:
                        $playerScore1->setVictories(1);
                        $playerScore2->setVictories(0);
                        $playerScore1->setDefeats(0);
                        $playerScore2->setDefeats(1);
                        $playerScore1->setTies(0);
                        $playerScore2->setTies(0);
                        $playerScore1->setScore(3);
                        $playerScore2->setScore(0);

                        break;

                    case MatchResultEnum::BLACK_WIN:
                        $playerScore1->setVictories(0);
                        $playerScore2->setVictories(1);
                        $playerScore1->setDefeats(1);
                        $playerScore2->setDefeats(0);
                        $playerScore1->setTies(0);
                        $playerScore2->setTies(0);
                        $playerScore1->setScore(0);
                        $playerScore2->setScore(3);
                        break;

                    case MatchResultEnum::TIE:
                        $playerScore1->setVictories(0);
                        $playerScore2->setVictories(0);
                        $playerScore1->setDefeats(0);
                        $playerScore2->setDefeats(0);
                        $playerScore1->setTies(1);
                        $playerScore2->setTies(1);
                        $playerScore1->setScore(1);
                        $playerScore2->setScore(1);
                        break;

                    default:
                        throw new CustomBadRequestException('Error in result of the match');
                }
                $score= [$playerScore1, $playerScore2];
                yield $score;
            }
        }

    }


    public function scoreNextRound($tournamentMatchCurrentRound, $tournament)
    {
        foreach ($tournamentMatchCurrentRound as $currentRound)
        {

            $playerScore1Previous = $this->playerScoreRepository->findPlayerByIdAndPreviousRound(
                $currentRound->getWhite(), $tournament->getCurrentRound()-1, $tournament->getId());
            $playerScore2Previous = $this->playerScoreRepository->findPlayerByIdAndPreviousRound(
                $currentRound->getBlack(), $tournament->getCurrentRound()-1, $tournament->getId());
            $playerScore1 = new PlayerScore();
            $playerScore2 = new PlayerScore();
            $playerScore1->setPlayer($playerScore1Previous->getPlayer());
            $playerScore2->setPlayer($playerScore2Previous->getPlayer());
            $playerScore1->setTournamentId($playerScore1Previous->getTournamentId());
            $playerScore2->setTournamentId($playerScore2Previous->getTournamentId());
            $playerScore1->setRound($playerScore1Previous->getRound()+1);
            $playerScore2->setRound($playerScore2Previous->getRound()+1);
            switch ($currentRound->getResult())
            {

                case MatchResultEnum::WHITE_WIN:
                    $playerScore1->setVictories($playerScore1Previous->getVictories()+1);
                    $playerScore2->setVictories($playerScore2Previous->getVictories());
                    $playerScore1->setDefeats($playerScore2Previous->getDefeats());
                    $playerScore2->setDefeats($playerScore2Previous->getDefeats()+1);
                    $playerScore1->setTies($playerScore1Previous->getTies());
                    $playerScore2->setTies($playerScore2Previous->getTies());
                    $playerScore1->setScore($playerScore1Previous->getScore()+3);
                    $playerScore2->setScore($playerScore1Previous->getScore());

                    break;

                case MatchResultEnum::BLACK_WIN:

                    $playerScore1->setVictories($playerScore1Previous->getVictories());
                    $playerScore2->setVictories($playerScore2Previous->getVictories()+1);
                    $playerScore1->setDefeats($playerScore2Previous->getDefeats()+1);
                    $playerScore2->setDefeats($playerScore2Previous->getDefeats());
                    $playerScore1->setTies($playerScore1Previous->getTies());
                    $playerScore2->setTies($playerScore2Previous->getTies());
                    $playerScore1->setScore($playerScore1Previous->getScore());
                    $playerScore2->setScore($playerScore1Previous->getScore()+3);
                    break;

                case MatchResultEnum::TIE:

                    $playerScore1->setVictories($playerScore1Previous->getVictories());
                    $playerScore2->setVictories($playerScore2Previous->getVictories());
                    $playerScore1->setDefeats($playerScore2Previous->getDefeats());
                    $playerScore2->setDefeats($playerScore2Previous->getDefeats());
                    $playerScore1->setTies($playerScore1Previous->getTies()+1);
                    $playerScore2->setTies($playerScore2Previous->getTies()+1);
                    $playerScore1->setScore($playerScore1Previous->getScore()+1);
                    $playerScore2->setScore($playerScore1Previous->getScore()+1);
                    break;

                default:
                    throw new CustomBadRequestException('Error in result of the match');
            }
            $score= [$playerScore1, $playerScore2];
            yield $score;
        }
    }



}