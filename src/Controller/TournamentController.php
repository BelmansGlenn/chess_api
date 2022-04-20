<?php

namespace App\Controller;

use App\DTO\Mapper\Player\PlayerSimpleDTOMapper;
use App\DTO\Mapper\Tournament\TournamentFullDTOMapper;
use App\DTO\Mapper\Tournament\TournamentSimpleDTOMapper;
use App\Entity\Tournament;
use App\Repository\PlayerRepository;
use App\Repository\TournamentRepository;
use App\Service\EntityManager\EntityManagerService;
use App\Service\EntityManager\TournamentEntityService;
use App\Service\Pagination\PaginationService;
use App\Service\Tournament\TournamentService;
use App\Service\Violations\ViolationsService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\ConstraintViolationList;

class TournamentController extends AbstractFOSRestController
{
    private ViolationsService $checkViolations;

    private EntityManagerService $entityManager;

    private TournamentService $tournamentService;

    private TournamentEntityService $tournamentEntity;

    /**
     * @param ViolationsService $checkViolations
     * @param EntityManagerService $entityManager
     * @param TournamentService $tournamentService
     * @param TournamentEntityService $tournamentEntity
     */
    public function __construct(ViolationsService $checkViolations, EntityManagerService $entityManager, TournamentService $tournamentService, TournamentEntityService $tournamentEntity)
    {
        $this->checkViolations = $checkViolations;
        $this->entityManager = $entityManager;
        $this->tournamentService = $tournamentService;
        $this->tournamentEntity = $tournamentEntity;
    }


    #[Post('/api/tournament', name: 'app_tournament_create')]
    #[IsGranted("ROLE_MODERATOR", message: "You don't have the right to access this page")]
    #[View(statusCode: 201)]
    #[ParamConverter("tournament", converter: "fos_rest.request_body")]
    public function createTournament(Tournament $tournament, ConstraintViolationList $violations)
    {
        $this->checkViolations->checkViolation($violations);

        $this->entityManager->create($tournament);

        return TournamentSimpleDTOMapper::transformFromObject($tournament);
    }

    #[Get('/api/tournament/{id}', name: 'app_tournament_get')]
    #[View(statusCode: 201)]
    public function getTournament(Tournament $tournament)
    {
        $this->checkViolations->checkIfExist($tournament->getId(), Tournament::class);

        return TournamentSimpleDTOMapper::transformFromObject($tournament);
    }



    #[Get('/api/tournament', name: 'app_tournaments_all')]
    #[QueryParam(name: "keyword",requirements: "[a-zA-Z0-9]", nullable: true, description: "the keyword to search for")]
    #[QueryParam(name: "order",requirements: "asc|desc", default: "asc", description: "Sort order (asc or desc)")]
    #[QueryParam(name: "limit",requirements: "\d+", default: "5", description: "Max number of articles per page")]
    #[QueryParam(name: "offset",requirements: "\d+", default: "1", description: "the pagination offset")]
    #[View(statusCode: 200)]
    public function getAllTournaments(ParamFetcherInterface $paramFetcher, TournamentRepository $repository, Request $request)
    {

        return PaginationService::paginate($repository, $paramFetcher, $request, fn($it) => TournamentSimpleDTOMapper::transformFromObject($it));
    }

    #[Get('/api/tournament/{id}/players', name: 'app_tournament')]
    #[QueryParam(name: "keyword",requirements: "[a-zA-Z0-9]", nullable: true, description: "the keyword to search for")]
    #[QueryParam(name: "order",requirements: "asc|desc", default: "asc", description: "Sort order (asc or desc)")]
    #[QueryParam(name: "limit",requirements: "\d+", default: "5", description: "Max number of articles per page")]
    #[QueryParam(name: "offset",requirements: "\d+", default: "1", description: "the pagination offset")]
    #[View(statusCode: 200)]
    public function getPlayersTournament(Tournament $tournament, ParamFetcherInterface $paramFetcher, PlayerRepository $repository, Request $request)
    {
        $tournament = $this->checkViolations->checkIfExist($tournament->getId(), Tournament::class);


        return PaginationService::paginate($repository, $paramFetcher, $request, fn($it) => PlayerSimpleDTOMapper::transformFromObject($it), ['id' => $tournament->getId()]);
    }

    #[Get('/api/tournament/join/{id}', name: 'app_tournament_join')]
    #[View(statusCode: 201)]
    public function joinTournament(Tournament $tournament)
    {
        $this->checkViolations->checkIfExist($tournament->getId(), Tournament::class);

        $canPlay = $this->tournamentService->canPlay($this->getUser(), $tournament);

        if ($canPlay === true)
        {
            $this->tournamentEntity->addPlayerToTournament($this->getUser(), $tournament);
            $this->entityManager->update();
        }
        else
        {
            throw new AccessDeniedException('You don\'t meet the requirements for this tournament');
        }

        return TournamentSimpleDTOMapper::transformFromObject($tournament);
    }

    #[Get('/api/tournament/leave/{id}', name: 'app_tournament_leave')]
    #[View(statusCode: 201)]
    public function leaveTournament(Tournament $tournament)
    {
        $this->checkViolations->checkIfExist($tournament->getId(), Tournament::class);

        $this->checkViolations->checkPlayerIsInTournament($tournament->getId(), $this->getUser()->getId());

        $this->tournamentEntity->removePlayerFromTournament($this->getUser(), $tournament);

        return TournamentSimpleDTOMapper::transformFromObject($tournament);
    }




}
