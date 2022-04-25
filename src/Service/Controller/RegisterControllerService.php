<?php

namespace App\Service\Controller;

use App\DTO\Mapper\Player\PlayerSimpleDTOMapper;
use App\Entity\Player;
use App\Exception\CustomBadRequestException;
use App\Service\EntityManager\EntityManagerService;
use App\Service\EntityManager\PlayerEntityService;
use App\Service\Mailer\VerificationMailService;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegisterControllerService
{

    private PlayerEntityService $playerEntityService;

    private EntityManagerService $entityManagerService;

    private VerificationMailService $verificationMailService;

    /**
     * @param PlayerEntityService $playerEntityService
     * @param EntityManagerService $entityManagerService
     * @param VerificationMailService $verificationMailService
     */
    public function __construct(PlayerEntityService $playerEntityService, EntityManagerService $entityManagerService, VerificationMailService $verificationMailService)
    {
        $this->playerEntityService = $playerEntityService;
        $this->entityManagerService = $entityManagerService;
        $this->verificationMailService = $verificationMailService;
    }


    public function register($playerDTO)
    {
        try {
            $player = $this->playerEntityService->registerPlayer($playerDTO);
            $this->entityManagerService->create($player);
            $this->verificationMailService->sendingVerificationEmail($player);

        }catch (CustomBadRequestException $e)
        {
            throw new CustomBadRequestException($e);
        }

       return PlayerSimpleDTOMapper::transformFromObject($player);
    }

    public function verifyPlayerEmail($uri, Player $player)
    {
        try {
            $this->verificationMailService->verifyEmail()->validateEmailConfirmation(
                $uri,
                $player->getId(),
                $player->getEmail()
            );
            $player->setIsVerified(true);
            $this->entityManagerService->update();
        }catch (VerifyEmailExceptionInterface $e)
        {
            throw new CustomBadRequestException($e->getReason());
        }
    }
}