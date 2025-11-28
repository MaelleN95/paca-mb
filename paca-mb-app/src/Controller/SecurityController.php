<?php

namespace App\Controller;
use App\Form\ResetPasswordType;
use App\Form\ForgotPasswordType;
use App\Entity\PasswordResetToken;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PasswordResetTokenRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'recaptcha_site_key' => $_ENV['GOOGLE_RECAPTCHA_SITE_KEY'],
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/forgot-password', name: 'forgot_password')]
    public function forgot(Request $request, UserRepository $users, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $users->findOneBy(['email' => $email]);

            $message = 'Si un compte existe, un email de réinitialisation a été envoyé.';
            
            if ($user) {
                $token = bin2hex(random_bytes(32));

                $tokenEntity = new PasswordResetToken();
                $tokenEntity->setUser($user);
                $tokenEntity->setTokenHash(password_hash($token, PASSWORD_DEFAULT));
                $tokenEntity->setExpiresAt(new \DateTimeImmutable('+1 hour'));

                $em->persist($tokenEntity);
                $em->flush();

                $resetUrl = $this->generateUrl('reset_password', [
                    'token' => $token
                ], UrlGeneratorInterface::ABSOLUTE_URL);

                $email = (new TemplatedEmail())
                    ->from(new Address('contact@koji-dev.fr', 'PACA Machines à bois'))
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de ton mot de passe')
                    ->htmlTemplate('emails/reset_password.html.twig')
                    ->context([
                        'resetUrl' => $resetUrl,
                    ]);

                $mailer->send($email);
            }

            $this->addFlash('info', $message);
            return $this->redirectToRoute('forgot_password');
        }

        return $this->render('security/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }


#[Route('/reset-password/{token}', name: 'reset_password')]
public function reset(string $token, Request $request, PasswordResetTokenRepository $tokens, UserPasswordHasherInterface $hasher,    EntityManagerInterface $em): Response {
        $tokenEntity = $tokens->findValidToken($token);

        if (!$tokenEntity || $tokenEntity->isExpired()) {
            return $this->render('error/reset_token_invalid.html.twig');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $tokenEntity->getUser();
            $newPassword = $form->get('password')->getData();

            $user->setPassword(
                $hasher->hashPassword($user, $newPassword)
            );

            $em->remove($tokenEntity);
            $em->flush();

            $this->addFlash('success', 'Mot de passe réinitialisé.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
