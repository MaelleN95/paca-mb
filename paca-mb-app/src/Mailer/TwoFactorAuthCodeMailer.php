<?php

namespace App\Mailer;

use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class TwoFactorAuthCodeMailer implements AuthCodeMailerInterface
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendAuthCode(TwoFactorInterface $user): void
    {
        $code = $user->getEmailAuthCode();

        $email = (new TemplatedEmail())
            ->from('contact@koji-dev.fr', 'PACA Machines à bois')
            ->to($user->getEmailAuthRecipient())
            ->subject('Votre code de connexion à MachinesBois')
            ->htmlTemplate('emails/2fa_code.html.twig')
            ->context([
                'auth_code' => $code,
                'user' => $user,
            ]);

        $this->mailer->send($email);
    }
}
