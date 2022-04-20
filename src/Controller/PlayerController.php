<?php

namespace App\Controller;

use App\DTO\Player\PlayerDTOUpdate;
use App\Entity\Player;
use App\Exception\CustomBadRequestException;
use App\Exception\PlayerNotUserConnectedException;
use App\Service\Controller\PlayerControllerService;
use App\Service\Violations\ViolationsService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\ConstraintViolationList;

class PlayerController extends AbstractFOSRestController
{
    private PlayerControllerService $playerControllerService;

    private ViolationsService $violationsService;

    /**
     * @param PlayerControllerService $playerControllerService
     * @param ViolationsService $violationsService
     */
    public function __construct(PlayerControllerService $playerControllerService, ViolationsService $violationsService)
    {
        $this->playerControllerService = $playerControllerService;
        $this->violationsService = $violationsService;
    }


    #[Get('/api/player/{id}', name: 'app_player', requirements: ['id ' => "\d+"])]
    #[View]
    public function getPlayer(Player $player)
    {
        try {
            $this->violationsService->checkPlayerIsUserConnected($this->getUser(),$player);
            return  $this->playerControllerService->getPlayer($this->getUser(), $player);
        }
        catch (PlayerNotUserConnectedException $e){
            throw new AccessDeniedException($e->getMessage());
        }

    }

    #[Patch('/api/player/{id}', name: 'app_player_update', requirements: ['id ' => "\d+"])]
    #[View]
    #[ParamConverter("playerDTO", converter: "fos_rest.request_body")]
    public function updatePlayer(Player $player, PlayerDTOUpdate $playerDTO, Request $request, ConstraintViolationList $violations)
    {
        try {
            $this->violationsService->checkPlayerIsUserConnected($this->getUser(), $player);
            $this->violationsService->checkViolation($violations);
            return $this->playerControllerService->updatePlayer($player, $playerDTO);
        }catch (PlayerNotUserConnectedException $e)
        {
            throw new AccessDeniedException($e->getMessage());
        }catch (CustomBadRequestException $e)
        {
            throw new BadRequestException($e->getMessage());
        }

    }

    #[Delete('/api/player/{id}', name: 'app_player_delete', requirements: ['id ' => "\d+"])]
    #[View]
    public function deletePlayer(Player $player)
    {
        $this->checkViolations->checkPlayerIsUserConnected($player);

        $this->entityManager->delete($player);


    }
}
