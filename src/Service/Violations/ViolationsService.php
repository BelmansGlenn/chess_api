<?php

namespace App\Service\Violations;

use App\Exception\CustomBadRequestException;
use App\Exception\PlayerNotUserConnectedException;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ViolationsService
{
    private EntityManagerInterface $entityManager;
    private TournamentRepository $tournamentRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param TournamentRepository $tournamentRepository
     */
    public function __construct(EntityManagerInterface $entityManager, TournamentRepository $tournamentRepository)
    {
        $this->entityManager = $entityManager;
        $this->tournamentRepository = $tournamentRepository;
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
        $object = $this->entityManager->getRepository($class)->find($id);

        if (!$object)
        {
            throw new CustomBadRequestException();
        }
        return $object;
    }

    public function checkPlayerIsUserConnected($user, $player)
    {
        if ($user !== $player)
        {
            throw new PlayerNotUserConnectedException();
        }
    }

    public function checkPlayerIsInTournament($tournamentId,$playerId){

        $player = $this->tournamentRepository->findPlayerByTournament($tournamentId, $playerId);

        if (!$player){
            throw new CustomBadRequestException('You cannot leave a tournament you didn\'t join');
        }

        return $player;
    }




}