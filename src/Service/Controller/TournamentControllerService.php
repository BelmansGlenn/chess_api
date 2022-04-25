<?php

namespace App\Service\Controller;

use App\DTO\Mapper\Player\PlayerSimpleDTOMapper;
use App\DTO\Mapper\Tournament\TournamentSimpleDTOMapper;
use App\Entity\Tournament;
use App\Exception\RulesTournamentException;
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

    /**
     * @param EntityManagerService $entityManagerService
     * @param TournamentRepository $tournamentRepository
     * @param TournamentService $tournamentService
     * @param TournamentEntityService $tournamentEntityService
     */
    public function __construct(EntityManagerService $entityManagerService, TournamentRepository $tournamentRepository, TournamentService $tournamentService, TournamentEntityService $tournamentEntityService)
    {
        $this->entityManagerService = $entityManagerService;
        $this->tournamentRepository = $tournamentRepository;
        $this->tournamentService = $tournamentService;
        $this->tournamentEntityService = $tournamentEntityService;
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

    public function getPlayersTournament($paramFetcher, $request, Tournament $tournament)
    {
        return PaginationService::paginate($this->tournamentRepository, $paramFetcher, $request, fn($it) => PlayerSimpleDTOMapper::transformFromObject($it), ['id' => $tournament->getId()]);
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
        return TournamentSimpleDTOMapper::transformFromObject($tournament);
    }

}