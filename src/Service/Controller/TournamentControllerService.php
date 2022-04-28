<?php

namespace App\Service\Controller;

use App\DTO\Mapper\Player\PlayerSimpleDTOMapper;
use App\DTO\Mapper\PlayerScore\PlayerScoreSimpleDTOMapper;
use App\DTO\Mapper\Tournament\TournamentSimpleDTOMapper;
use App\DTO\Mapper\TournamentMatch\TournamentMatchSimpleDTOMapper;
use App\Entity\MatchResultEnum;
use App\Entity\Tournament;
use App\Exception\CustomBadRequestException;
use App\Exception\RouteNotFoundException;
use App\Exception\RulesTournamentException;
use App\Repository\PlayerRepository;
use App\Repository\PlayerScoreRepository;
use App\Repository\TournamentMatchRepository;
use App\Repository\TournamentRepository;
use App\Service\EntityManager\EntityManagerService;
use App\Service\EntityManager\TournamentEntityService;
use App\Service\Pagination\PaginationService;
use App\Service\Tournament\TournamentService;

class TournamentControllerService
{
    private EntityManagerService $entityManagerService;
    private TournamentRepository $tournamentRepository;
    private TournamentService $tournamentService;
    private TournamentEntityService $tournamentEntityService;
    private PlayerRepository $playerRepository;
    private TournamentMatchRepository $tournamentMatchRepository;
    private PlayerScoreRepository $playerScoreRepository;

    /**
     * @param EntityManagerService $entityManagerService
     * @param TournamentRepository $tournamentRepository
     * @param TournamentService $tournamentService
     * @param TournamentEntityService $tournamentEntityService
     * @param PlayerRepository $playerRepository
     * @param TournamentMatchRepository $tournamentMatchRepository
     * @param PlayerScoreRepository $playerScoreRepository
     */
    public function __construct(EntityManagerService $entityManagerService, TournamentRepository $tournamentRepository, TournamentService $tournamentService, TournamentEntityService $tournamentEntityService, PlayerRepository $playerRepository, TournamentMatchRepository $tournamentMatchRepository, PlayerScoreRepository $playerScoreRepository)
    {
        $this->entityManagerService = $entityManagerService;
        $this->tournamentRepository = $tournamentRepository;
        $this->tournamentService = $tournamentService;
        $this->tournamentEntityService = $tournamentEntityService;
        $this->playerRepository = $playerRepository;
        $this->tournamentMatchRepository = $tournamentMatchRepository;
        $this->playerScoreRepository = $playerScoreRepository;
    }


    public function createTournament($tournament)
    {
        $this->entityManagerService->create($tournament);

        return TournamentSimpleDTOMapper::transformFromObject($tournament);
    }

    public function getTournament($tournament)
    {
        return TournamentSimpleDTOMapper::transformFromObject($tournament);
    }

    public function getAllTournaments($paramFetcher,$request)
    {
        return PaginationService::paginate($this->tournamentRepository, $paramFetcher, $request, fn($it) => TournamentSimpleDTOMapper::transformFromObject($it));
    }

    public function getPlayersTournament($paramFetcher, $request, $tournamentId)
    {

        return PaginationService::paginate($this->playerRepository, $paramFetcher, $request, fn($it) => PlayerSimpleDTOMapper::transformFromObject($it), ['id' => $tournamentId]);
    }

    public function joinTournament($player, $tournament)
    {
        try {
            $canPlay = $this->tournamentService->canPlay($player, $tournament);
        if ($canPlay === true )
        {
            $this->tournamentEntityService->addPlayerToTournament($player, $tournament);
            $this->entityManagerService->update();

        }

        }catch (RulesTournamentException $e)
        {
            throw new RulesTournamentException($e);
        }
        return TournamentSimpleDTOMapper::transformFromObject($tournament);
    }

    public function leaveTournament($player, $tournament)
    {
        $this->tournamentEntityService->removePlayerFromTournament($player, $tournament);
    }

    public function startTournament(Tournament $tournament)
    {
        try {
                $this->tournamentService->canStart($tournament);
                $tournament->setCurrentRound(1);
                $players = $tournament->getPlayers()->toArray();
                $count = count($players);
                $tournament->setMaxRound($count - 1);
                $tournamentMatch = $this->tournamentService->createTournamentMatches($players, $tournament, $count);
                foreach ($tournamentMatch as $match)
                {
                $this->entityManagerService->persist($match);
                }
                $this->entityManagerService->flush();

        }catch (RulesTournamentException $e)
        {
            throw new RulesTournamentException($e);
        }
    }

    public function updateResultTournamentMatch($result, $tournament, $tournamentMatch)
    {
        $result = MatchResultEnum::tryFrom($result);
        if (!$result)
        {
            throw new RouteNotFoundException();
        }
        try {
            $this->tournamentService->canUpdate($result, $tournament, $tournamentMatch);
            $this->entityManagerService->update();

        }catch(RouteNotFoundException $e)
        {
            throw new RouteNotFoundException($e);
        }
        return TournamentMatchSimpleDTOMapper::transformFromObject($tournamentMatch);

    }

    public function roundTournament($paramFetcher, $request, $tournamentId)
    {
        return PaginationService::paginate($this->tournamentMatchRepository, $paramFetcher, $request,
            fn($it) => TournamentMatchSimpleDTOMapper::transformFromObject($it), ['round' => $paramFetcher->get('keyword'), 'id' => $tournamentId]);
    }



    public function nextRoundTournamentMatch(Tournament $tournament)
    {
        try {

            if ($tournament->getCurrentRound() < $tournament->getMaxRound()) {
                $tournamentMatchCurrentRound = $this->tournamentMatchRepository->findTournamentMatchCurrentRound($tournament->getCurrentRound(), $tournament->getId());

                $this->tournamentService->isAllMatchPlayed($tournamentMatchCurrentRound);

                $tournamentMatchPreviousRound = null;

                if ($tournament->getCurrentRound() > 1)
                {
                    $tournamentMatchPreviousRound = $this->playerScoreRepository->findTournamentPreviousRound($tournament->getCurrentRound()-1, $tournament->getId());
                }
                if ($tournamentMatchPreviousRound === null)
                {
                    $scores = $this->tournamentService->scoreFirstRound($tournament, $tournamentMatchCurrentRound, $tournamentMatchPreviousRound);

                }else{
                    $scores = $this->tournamentService->scoreNextRound($tournamentMatchCurrentRound, $tournament);

                }
                foreach ($scores as $score)
                {
                    foreach ($score as $playerScore)
                    {
                        $this->entityManagerService->persist($playerScore);

                    }
                }
                $this->entityManagerService->flush();

                $tournament->setCurrentRound($tournament->getCurrentRound() + 1);
                $this->entityManagerService->update();

            }


            if ($tournament->getIsFinished() === true)
            {
                throw new RulesTournamentException('The tournament is over. All the round has been played');
            }
            if ($tournament->getCurrentRound() === $tournament->getMaxRound())
            {
                $tournament->setIsFinished(true);
                $this->entityManagerService->update();
            }
        }catch(RulesTournamentException $e)
        {
            throw new RulesTournamentException($e);
        }catch (CustomBadRequestException $e)
        {
            throw new CustomBadRequestException($e);
        }



    }

    public function scorePerRound($paramFetcher, $request, $tournament)
    {
        if ($tournament->getCurrentRound() < 0)
        {
            throw new CustomBadRequestException('The tournament has not yet started.');
        }
        return PaginationService::paginate($this->playerScoreRepository, $paramFetcher, $request,
            fn($it) => PlayerScoreSimpleDTOMapper::transformFromObject($it), ['round' => $paramFetcher->get('keyword'), 'id' => $tournament->getId()]);


    }


}