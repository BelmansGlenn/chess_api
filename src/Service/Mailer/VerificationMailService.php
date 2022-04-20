<?php

namespace App\Service\Mailer;

use App\Entity\Player;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class VerificationMailService
{

    private VerifyEmailHelperInterface $verifyEmailHelper;
    private MailerInterface $mailer;

    public function __construct(VerifyEmailHelperInterface $helper, MailerInterface $mailer)
    {
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
    }

    public function sendingVerificationEmail(Player $player)
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'registration_confirmation_route',
            $player->getId(),
            $player->getEmail(),
            ['id' => $player->getId()]
        );
        $email = (new Email())
            ->from('api@gmail.com')
            ->to($player->getEmail())
            ->subject("Verify")
            ->html(sprintf('<h1>Bonjour!</h1>

<p>
    Please verify your account by clicking on this link<br><br>
    <a href= "%s" >Confirm your account</a>.
    This link expires in 1h
</p>

<p>
    See you soon!
</p>', $signatureComponents->getSignedUrl())
            );
        $this->mailer->send($email);
    }


    public function verifyEmail()
    {
        return $this->verifyEmailHelper;
    }
}
