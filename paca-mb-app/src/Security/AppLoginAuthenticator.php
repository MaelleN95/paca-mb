<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Karser\Recaptcha3Bundle\ReCaptcha\ReCaptcha;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class AppLoginAuthenticator extends AbstractLoginFormAuthenticator
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private ReCaptcha $recaptcha
    ) {}
    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'app_login'
            && $request->isMethod('POST');
    }
    
    public function authenticate(Request $request): Passport
    {
        $captchaToken = $request->request->get('g-recaptcha-response');

        $response = $this->recaptcha->verify($captchaToken, 'login');
        dd($response->getScore(), $response->isSuccess(), $response->getErrorCodes());

        // Valider le token
        if (!$this->recaptcha->verify($captchaToken, 'login')->isSuccess()) {
            throw new AuthenticationException('Captcha invalide');
        }

        return new Passport(
            new UserBadge($request->request->get('_username')),
            new PasswordCredentials($request->request->get('_password')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): Response
    {
        return new Response('', 302, ['Location' => $this->urlGenerator->generate('app_home')]);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('app_login');
    }
}
