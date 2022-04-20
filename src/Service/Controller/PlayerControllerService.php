<?php

namespace App\Service\Controller;

use App\DTO\Mapper\Player\PlayerSimpleDTOMapper;
use App\Entity\Player;
use App\Service\EntityManager\EntityManagerService;
use App\Service\EntityManager\PlayerEntityService;
use App\Service\FileUpload\FileUploadService;
use App\Service\Violations\ViolationsService;

class PlayerControllerService
{
    private PlayerEntityService $playerEntityService;

    private FileUploadService $uploadService;

    private EntityManagerService $entityManagerService;

    /**
     * @param PlayerEntityService $playerEntityService
     * @param FileUploadService $uploadService
     * @param EntityManagerService $entityManagerService
     */
    public function __construct(PlayerEntityService $playerEntityService, FileUploadService $uploadService, EntityManagerService $entityManagerService)
    {
        $this->playerEntityService = $playerEntityService;
        $this->uploadService = $uploadService;
        $this->entityManagerService = $entityManagerService;
    }


    public function getPlayer($user, $player)
    {
        return PlayerSimpleDTOMapper::transformFromObject($player);
    }

    public function updatePlayer($player, $playerDTO)
    {
        if ($playerDTO->getImage() !== null || $playerDTO->getPlainPassword() !== null )
        {
            $this->playerEntityService->updatePlayer($player, $playerDTO);

            $this->entityManagerService->update();
            return PlayerSimpleDTOMapper::transformFromObject($player);
        }


    }





}