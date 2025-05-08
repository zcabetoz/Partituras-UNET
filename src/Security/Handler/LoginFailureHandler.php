<?php

namespace App\Security\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Routing\RouterInterface;

class LoginFailureHandler implements AuthenticationFailureHandlerInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        $request->getSession()->getFlashBag()->add('error', 'Credenciales inválidas.');

        return new RedirectResponse($this->router->generate('nucleos_user_security_login'));
    }
}