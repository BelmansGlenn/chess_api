<?php

namespace App\Controller;

use App\DTO\TournamentMatch\TournamentMatchDTOUpdate;
use App\Entity\MatchResultEnum;
use App\Entity\Tournament;
use App\Entity\TournamentMatch;
use App\Exception\CustomBadRequestException;
use App\Exception\RouteNotFoundException;
use App\Exception\RulesTournamentException;
use App\Service\Controller\TournamentControllerService;
use App\Service\Violations\ViolationsService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\ConstraintViolationList;

class TournamentController extends AbstractFOSRestController
{
    private ViolationsService $checkViolations;

    private TournamentControllerService $tournamentControllerService;

    /**
     * @param ViolationsService $checkViolations
     * @param TournamentControllerService $tournamentControllerService
     */
    public function __construct(ViolationsService $checkViolations, TournamentControllerService $tournamentControllerService)
    {
        $this->checkViolations = $checkViolations;
        $this->tournamentControllerService = $tournamentControllerService;
    }


    #[Post('/api/tournament', name: 'app_tournament_create')]
    #[IsGranted("ROLE_MODERATOR", message: "You don't have the right to access this page")]
    #[View(statusCode: 201)]
    #[ParamConverter("tournament", converter: "fos_rest.request_body")]
    public function createTournament(Tournament $tournament, ConstraintViolationList $violations)
    {
        try {
            $this->checkViolations->checkViolation($violations);
            return $this->tournamentControllerService->createTournament($tournament);
        }catch(CustomBadRequestException $e)
        {
            throw new BadRequestException($e);
        }

    }

    #[Get('/api/tournament/{id}', name: 'app_tournament_get')]
    #[View(statusCode: 200)]
    public function getTournament($id)
    {
        $tournament = $this->checkViolations->checkIfExist($id, Tournament::class);
        return $this->tournamentControllerService->getTournament($tournament);
    }



    #[Get('/api/tournament', name: 'app_tournaments_all')]
    #[QueryParam(name: "keyword",requirements: "[a-zA-Z0-9]", nullable: true, description: "the keyword to search for")]
    #[QueryParam(name: "order",requirements: "asc|desc", default: "asc", description: "Sort order (asc or desc)")]
    #[QueryParam(name: "limit",requirements: "\d+", default: "5", description: "Max number of articles per page")]
    #[QueryParam(name: "offset",requirements: "\d+", default: "1", description: "the pagination offset")]
    #[View(statusCode: 200)]
    public function getAllTournaments(ParamFetcherInterface $paramFetcher, Request $request)
    {
        return $this->tournamentControllerService->getAllTournaments($paramFetcher, $request);
    }

    #[Get('/api/tournament/{id}/players', name: 'app_tournament')]
    #[QueryParam(name: "keyword",requirements: "[a-zA-Z0-9]", nullable: true, description: "the keyword to search for")]
    #[QueryParam(name: "order",requirements: "asc|desc", default: "asc", description: "Sort order (asc or desc)")]
    #[QueryParam(name: "limit",requirements: "\d+", default: "5", description: "Max number of articles per page")]
    #[QueryParam(name: "offset",requirements: "\d+", default: "1", description: "the pagination offset")]
    #[View(statusCode: 200)]
    public function getPlayersTournament($id, ParamFetcherInterface $paramFetcher,Request $request)
    {
        try {

            $this->checkViolations->checkIfExist($id, Tournament::class);
            return $this->tournamentControllerService->getPlayersTournament($paramFetcher, $request, $id);

        }catch (CustomBadRequestException $e){
            throw new BadRequestException($e);
        }

    }

    #[Patch('/api/tournament/join/{id}', name: 'app_tournament_join')]
    #[View(statusCode: 201)]
    public function joinTournament($id)
    {
        try {
            $tournament = $this->checkViolations->checkIfExist($id, Tournament::class);
            $result = $this->checkViolations->checkPlayerIsInTournament($id, $this->getUser()->getId());
            if ($result === true)
            {
                throw new BadRequestException('Already in this tournament.');
            }
            return $this->tournamentControllerService->joinTournament($this->getUser(), $tournament);
        }catch(CustomBadRequestException $e)
        {
            throw new BadRequestException($e);
        }catch (RulesTournamentException $e)
        {
            throw new AccessDeniedException($e);
        }
    }

    #[Patch('/api/tournament/leave/{id}', name: 'app_tournament_leave')]
    #[View(statusCode: 204)]
    public function leaveTournament($id)
    {
        try {
            $tournament = $this->checkViolations->checkIfExist($id, Tournament::class);
            $result = $this->checkViolations->checkPlayerIsInTournament($id, $this->getUser()->getId());
            if ($result === false)
            {
                throw new BadRequestException('Cannot leave a tournament you didn\'t join.');
            }

            $this->tournamentControllerService->leaveTournament($this->getUser(), $tournament);

        }catch (CustomBadRequestException $e)
        {
            throw new BadRequestException($e);
        }

    }

    #[Get('/api/tournament/{tournamentId}/start', name: 'app_tournament_start')]
    #[View(statusCode: 201)]
    public function startTournament($tournamentId)
    {
        try {
            $tournament = $this->checkViolations->checkIfExist($tournamentId, Tournament::class);
            $this->tournamentControllerService->startTournament($tournament);
        }catch (CustomBadRequestException $e)
        {
            throw new BadRequestException($e);
        }catch (RulesTournamentException $e)
        {
            throw new AccessDeniedException($e);
        }
    }

    #[Get('/api/tournament/{tournamentId}/round', name: 'app_tournament_round')]
    #[QueryParam(name: "keyword",requirements: "\d+", default: "1", description: "the keyword to search for")]
    #[QueryParam(name: "order",requirements: "asc|desc", default: "asc", description: "Sort order (asc or desc)")]
    #[QueryParam(name: "limit",requirements: "\d+", default: "5", description: "Max number of articles per page")]
    #[QueryParam(name: "offset",requirements: "\d+", default: "1", description: "the pagination offset")]
    #[View(statusCode: 200)]
    public function roundTournament($tournamentId, ParamFetcherInterface $paramFetcher, Request $request)
    {
        try {
            $this->checkViolations->checkIfExist($tournamentId, Tournament::class);
            return $this->tournamentControllerService->roundTournament($paramFetcher, $request, $tournamentId);
        }catch(CustomBadRequestException $e)
        {
            throw new BadRequestException($e);
        }
    }

    #[Patch('/api/tournament/{tournamentId}/update_result', name: 'app_tournament_update_match')]
    #[ParamConverter("tournamentMatchDTO", converter: "fos_rest.request_body")]
    #[View(statusCode: 200)]
    public function updateResultTournamentMatch($tournamentId, TournamentMatchDTOUpdate $tournamentMatchDTO)
    {
        try {
        $tournament = $this->checkViolations->checkIfExist($tournamentId, Tournament::class);
        $tournamentMatch = $this->checkViolations->checkIfExist($tournamentMatchDTO->getMatchId(), TournamentMatch::class);
        return $this->tournamentControllerService->updateResultTournamentMatch(
            $tournamentMatchDTO->getResult(), $tournament, $tournamentMatch
        );
        }catch(CustomBadRequestException $e)
        {
            throw new BadRequestException($e);
        }catch (RouteNotFoundException $e)
        {
            throw new NotFoundHttpException($e);
        }

    }


    #[Get('/api/tournament/{tournamentId}/next', name: 'app_tournament_next_round')]
    #[View(statusCode: 200)]
    public function nextRoundTournamentMatch($tournamentId)
    {
        try {
            $tournament = $this->checkViolations->checkIfExist($tournamentId, Tournament::class);
            $this->tournamentControllerService->nextRoundTournamentMatch($tournament);
        }catch (CustomBadRequestException $e)
        {
            throw new BadRequestException($e);
        }catch (RulesTournamentException $e)
        {
            throw new AccessDeniedException($e);
        }
    }

    #[Get('/api/tournament/{tournamentId}/score')]
    #[QueryParam(name: "keyword",requirements: "\d+", default: "1", description: "the keyword to search for")]
    #[QueryParam(name: "order",requirements: "asc|desc", default: "asc", description: "Sort order (asc or desc)")]
    #[QueryParam(name: "limit",requirements: "\d+", default: "5", description: "Max number of articles per page")]
    #[QueryParam(name: "offset",requirements: "\d+", default: "1", description: "the pagination offset")]
    #[View(statusCode: 200)]
    public function scorePerRound($tournamentId, ParamFetcherInterface $paramFetcher, Request $request)
    {
        try {
            $tournament = $this->checkViolations->checkIfExist($tournamentId, Tournament::class);
            return $this->tournamentControllerService->scorePerRound($paramFetcher, $request, $tournament);
        }catch(CustomBadRequestException $e)
        {
            throw new BadRequestException($e);
        }
    }


}
