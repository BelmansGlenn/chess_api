<?php

namespace App\Service\Controller;

use App\DTO\Mapper\Player\PlayerSimpleDTOMapper;
use App\Exception\CustomBadRequestException;
use App\Service\EntityManager\EntityManagerService;
use App\Service\EntityManager\PlayerEntityService;

class PlayerControllerService
{
    private PlayerEntityService $playerEntityService;

    private EntityManagerService $entityManagerService;

    /**
     * @param PlayerEntityService $playerEntityService
     * @param EntityManagerService $entityManagerService
     */
    public function __construct(PlayerEntityService $playerEntityService,  EntityManagerService $entityManagerService)
    {
        $this->playerEntityService = $playerEntityService;
        $this->entityManagerService = $entityManagerService;
    }


    public function getPlayer($player)
    {
        return PlayerSimpleDTOMapper::transformFromObject($player);
    }

    public function updatePlayer($player, $playerDTO)
    {
        if ($playerDTO->getImage() !== null || $playerDTO->getPlainPassword() !== null )
        {
            $this->playerEntityService->updatePlayer($player, $playerDTO);

            $this->entityManagerService->update();

        }else{
            throw new CustomBadRequestException('Already up to date');
        }
        return PlayerSimpleDTOMapper::transformFromObject($player);

    }

    public function deletePlayer($player)
    {
        $this->entityManagerService->delete($player);
    }





}