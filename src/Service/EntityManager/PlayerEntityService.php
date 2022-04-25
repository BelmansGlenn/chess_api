<?php

namespace App\Service\EntityManager;

use App\DTO\Player\PlayerDTOCreate;
use App\DTO\Player\PlayerDTOUpdate;
use App\Entity\Player;
use App\Service\FileUpload\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PlayerEntityService
{

    private UserPasswordHasherInterface $passwordHasher;

    private FileUploadService $uploadService;


    /**
     * @param UserPasswordHasherInterface $passwordHasher
     * @param FileUploadService $uploadService
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher, FileUploadService $uploadService)
    {
        $this->passwordHasher = $passwordHasher;
        $this->uploadService = $uploadService;
    }


    public function registerPlayer(PlayerDTOCreate $dto): Player
    {
        $player = new Player();
        $player->setEmail($dto->getEmail())
            ->setFirstname($dto->getFirstname())
            ->setLastname($dto->getLastname())
            ->setGender($dto->getGender())
            ->setBirthday($dto->getBirthday())
            ->setImage($this->uploadService->defaultImg());
        $player->setPassword($this->passwordHasher->hashPassword(
            $player, $dto->getPlainPassword())
        );

        return $player;

    }

    public function updatePlayer(Player $player,PlayerDTOUpdate $playerDTO)
    {
        if ($playerDTO->getPlainPassword() !== null){
        $player->setPassword($this->passwordHasher->hashPassword(
            $player, $playerDTO->getPlainPassword()
        ));
        }
        if ($playerDTO->getImage() !== null)
        {
            $filePath = $this->uploadService->uploadImgPlayer($playerDTO->getImage(), $player);
            $player->setImage($filePath);
        }

        return $player;

    }

}