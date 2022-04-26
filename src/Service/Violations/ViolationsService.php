<?php

namespace App\Service\Violations;

use App\Exception\CustomBadRequestException;
use App\Exception\PlayerNotUserConnectedException;
use App\Repository\PlayerRepository;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ViolationsService
{
    private EntityManagerInterface $entityManager;
    private TournamentRepository $tournamentRepository;
    private PlayerRepository $playerRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param TournamentRepository $tournamentRepository
     * @param PlayerRepository $playerRepository
     */
    public function __construct(EntityManagerInterface $entityManager, TournamentRepository $tournamentRepository, PlayerRepository $playerRepository)
    {
        $this->entityManager = $entityManager;
        $this->tournamentRepository = $tournamentRepository;
        $this->playerRepository = $playerRepository;
    }


    public function checkViolation($violations)
    {
        if (count($violations)){
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("\nField %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }
            throw new CustomBadRequestException($message);
        }
    }

    public function checkIfExist($id, $class)
    {
        $result = $this->entityManager->getRepository($class)->find($id);
        if ($result === null)
        {
            throw new CustomBadRequestException();
        }
        return $result;
    }

    public function checkPlayerIsUserConnected($user, $playerId)
    {
        $player = $this->playerRepository->find($playerId);
        if ($user !== $player)
        {
            throw new PlayerNotUserConnectedException();
        }
        return $player;
    }

    public function checkPlayerIsInTournament($tournamentId,$playerId){

        $result = $this->tournamentRepository->findOnePlayerByTournament($tournamentId, $playerId);

        if ($result === null){
            return false;
        }else{
            return true;
        }
    }




}