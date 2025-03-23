<?php

declare(strict_types=1);

namespace App\Service\EmailVerification;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;

final readonly class EmailVerificationService implements EmailVerificationServiceInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailerInterface $mailer,
        #[Autowire(env: 'FRONTEND_URL')]
        private string $frontendUrl,
    ) {
    }

    public function sendVerificationEmail(User $user): void
    {
        $token = UserService::generateVerificationToken();
        $expiresAt = new \DateTimeImmutable('+24 hours');

        $user->setVerificationToken($token);
        $user->setVerificationTokenExpiresAt($expiresAt);

        $verificationUrl = \sprintf(
            '%s/verify-email/%s',
            $this->frontendUrl,
            $token
        );

        $email = (new TemplatedEmail())
            ->to($user->getEmail())
            ->subject('Vérification de votre adresse email - FiqHub')
            ->htmlTemplate('emails/email_verification.html.twig')
            ->context([
                'verification_url' => $verificationUrl,
                'user' => $user,
            ]);

        $this->mailer->send($email);
        $this->entityManager->flush();
    }
}
