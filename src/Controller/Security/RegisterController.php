<?php

namespace App\Controller\Security;

use App\DTO\Player\PlayerDTOCreate;
use App\Entity\Player;
use App\Exception\CustomBadRequestException;
use App\Service\Controller\RegisterControllerService;
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

class RegisterController extends AbstractFOSRestController
{

    private ViolationsService $checkViolations;

    private RegisterControllerService $registerControllerService;

    /**
     * @param ViolationsService $checkViolations
     * @param RegisterControllerService $registerControllerService
     */
    public function __construct(ViolationsService $checkViolations, RegisterControllerService $registerControllerService)
    {
        $this->checkViolations = $checkViolations;
        $this->registerControllerService = $registerControllerService;
    }


    #[Post('/api/register', name: 'app_register')]
    #[View(statusCode: 201)]
    #[ParamConverter("playerDTO", converter: "fos_rest.request_body")]
    public function register(PlayerDTOCreate $playerDTO, ConstraintViolationList $violations)
    {
        try {
            $this->checkViolations->checkViolation($violations);

            return $this->registerControllerService->register($playerDTO);
        }catch (CustomBadRequestException $e){
            throw new BadRequestException($e->getMessage());
        }

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

        try {
            $player = $this->checkViolations->checkIfExist($id, Player::class);
            $this->registerControllerService->verifyPlayerEmail(
                $request->getUri(),
            $player);

        }
        catch (CustomBadRequestException $e)
        {
            throw new BadRequestException($e);
        }

    }
}
