<?php

namespace App\Service\EntityManager;

use App\Entity\Tournament;

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
        $tournament = $tournament->addPlayer($player);
        return $tournament;
    }

    public function removePlayerFromTournament($player, $tournament)
    {
        $tournament = $tournament->removePlayer($player);
        return $tournament;
    }


}