<?php

namespace App\Service\EntityManager;

use App\Entity\Tournament;
use App\Exception\CustomBadRequestException;
use App\Repository\TournamentRepository;

class TournamentEntityService
{
    private EntityManagerService $entityManagerService;

    /**
     * @param EntityManagerService $entityManagerService
     */
    public function __construct(EntityManagerService $entityManagerService)
    {
        $this->entityManagerService = $entityManagerService;
    }


    public function addPlayerToTournament($player, $tournament)
    {
        $tournament->addPlayer($player);
        $this->entityManagerService->update();
    }

    public function removePlayerFromTournament($playerId, $tournament)
    {
        $tournament->removePlayer($playerId);
        $this->entityManagerService->update();
    }


}