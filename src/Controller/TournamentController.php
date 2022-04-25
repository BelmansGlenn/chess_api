<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Exception\CustomBadRequestException;
use App\Exception\RulesTournamentException;
use App\Service\Controller\TournamentControllerService;
use App\Service\Violations\ViolationsService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
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
    #[View(statusCode: 201)]
    public function getTournament(Tournament $tournament)
    {
        $this->checkViolations->checkIfExist($tournament->getId(), Tournament::class);
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
        $this->tournamentControllerService->getAllTournaments($paramFetcher, $request);
    }

    #[Get('/api/tournament/{id}/players', name: 'app_tournament')]
    #[QueryParam(name: "keyword",requirements: "[a-zA-Z0-9]", nullable: true, description: "the keyword to search for")]
    #[QueryParam(name: "order",requirements: "asc|desc", default: "asc", description: "Sort order (asc or desc)")]
    #[QueryParam(name: "limit",requirements: "\d+", default: "5", description: "Max number of articles per page")]
    #[QueryParam(name: "offset",requirements: "\d+", default: "1", description: "the pagination offset")]
    #[View(statusCode: 200)]
    public function getPlayersTournament(Tournament $tournament, ParamFetcherInterface $paramFetcher,Request $request)
    {
        try {

            $this->checkViolations->checkIfExist($tournament->getId(), Tournament::class);
            return $this->tournamentControllerService->getPlayersTournament($paramFetcher, $request, $tournament);

        }catch (CustomBadRequestException $e){
            throw new BadRequestException($e);
        }

    }

    #[Get('/api/tournament/join/{id}', name: 'app_tournament_join')]
    #[View(statusCode: 201)]
    public function joinTournament(Tournament $tournament)
    {
        try {
        $this->checkViolations->checkIfExist($tournament->getId(), Tournament::class);
        return $this->tournamentControllerService->joinTournament($this->getUser(), $tournament);
        }catch(CustomBadRequestException $e)
        {
            throw new BadRequestException($e);
        }catch (RulesTournamentException $e)
        {
            throw new AccessDeniedException($e);
        }
    }

    #[Get('/api/tournament/leave/{id}', name: 'app_tournament_leave')]
    #[View(statusCode: 201)]
    public function leaveTournament(Tournament $tournament)
    {
        try {
            $this->checkViolations->checkIfExist($tournament->getId(), Tournament::class);

            $this->checkViolations->checkPlayerIsInTournament($tournament->getId(), $this->getUser()->getId());

            return $this->tournamentControllerService->leaveTournament($this->getUser(), $tournament);
        }catch (CustomBadRequestException $e)
        {
            throw new BadRequestException($e);
        }

    }




}
