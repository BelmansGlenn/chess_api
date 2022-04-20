<?php

namespace App\Controller\Security;

use App\DTO\Mapper\Player\PlayerSimpleDTOMapper;
use App\DTO\Player\PlayerDTOCreate;
use App\Entity\Player;
use App\Service\EntityManager\EntityManagerService;
use App\Service\EntityManager\PlayerEntityService;
use App\Service\FileUpload\FileUploadService;
use App\Service\Mailer\VerificationMailService;
use App\Service\Violations\ViolationsService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationList;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegisterController extends AbstractFOSRestController
{

    private ViolationsService $checkViolations;

    private PlayerEntityService $register;

    private FileUploadService $upload;

    private EntityManagerService $entityManager;

    private VerificationMailService $verificationMail;

    /**
     * @param ViolationsService $checkViolations
     * @param PlayerEntityService $register
     * @param FileUploadService $upload
     * @param EntityManagerService $entityManager
     * @param VerificationMailService $verificationMail
     */
    public function __construct(ViolationsService $checkViolations, PlayerEntityService $register, FileUploadService $upload, EntityManagerService $entityManager, VerificationMailService $verificationMail)
    {
        $this->checkViolations = $checkViolations;
        $this->register = $register;
        $this->upload = $upload;
        $this->entityManager = $entityManager;
        $this->verificationMail = $verificationMail;
    }


    #[Post('/api/register', name: 'app_register')]
    #[View(statusCode: 201)]
    #[ParamConverter("playerDTO", converter: "fos_rest.request_body")]
    public function register(PlayerDTOCreate $playerDTO, ConstraintViolationList $violations)
    {

        $this->checkViolations->checkViolation($violations);

        $player = $this->register->registerPlayer($playerDTO);

        if ($playerDTO->getImage())
        {
        $this->upload->uploadImgPlayer($playerDTO->getImage());
        }

        $this->entityManager->create($player);

        $this->verificationMail->sendingVerificationEmail($player);

        return PlayerSimpleDTOMapper::transformFromObject($player);
    }

    #[Get("/verify", name:"registration_confirmation_route")]
    #[View(statusCode: 204)]
    public function verifyPlayerEmail(Request $request)
    {
        $id = $request->get('id');

        if ($id === null)
        {
            throw new ParameterNotFoundException('id');
        }

        $player = $this->checkViolations->checkIfExist($id, Player::class);

        try {
            $this->verificationMail->verifyEmail()->validateEmailConfirmation(
                $request->getUri(),
                $player->getId(),
                $player->getEmail()
            );
            $player->setIsVerified(true);
            $this->entityManager->update();
        }
        catch (VerifyEmailExceptionInterface $exception)
        {
            throw new BadRequestException($exception->getReason());
        }

    }
}
