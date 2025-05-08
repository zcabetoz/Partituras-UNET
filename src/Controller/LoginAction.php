<?php

namespace App\Controller;

use Nucleos\UserBundle\Event\GetResponseLoginEvent;
use Nucleos\UserBundle\NucleosUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class LoginAction extends AbstractController
{
    private Environment $twig;
    private EventDispatcherInterface $eventDispatcher;
    private ?CsrfTokenManagerInterface $tokenManager;

    public function __construct(
        Environment                $twig,
        EventDispatcherInterface   $eventDispatcher,
        ?CsrfTokenManagerInterface $tokenManager = null
    )
    {
        $this->twig = $twig;
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function __invoke(Request $request): Response
    {
        $event = new GetResponseLoginEvent($request);
        $this->eventDispatcher->dispatch($event, NucleosUserEvents::SECURITY_LOGIN_INITIALIZE);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $session = $this->getSession($request);

        $authErrorKey = SecurityRequestAttributes::AUTHENTICATION_ERROR;
        $lastUsernameKey = SecurityRequestAttributes::LAST_USERNAME;

        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if ($request->isMethod('POST')) {
            return $this->redirectToRoute('nucleos_user_security_check', ['request' => $request], 307);
        }

        if (!$error instanceof AuthenticationException) {
            $error = null;
        }

        $lastUsername = $session?->get($lastUsernameKey) ?? '';

        return new Response($this->twig->render('@NucleosUser/Security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $this->getCsrfToken(),
        ]));
    }

    private function getSession(Request $request): ?SessionInterface
    {
        return $request->hasSession() ? $request->getSession() : null;
    }

    private function getCsrfToken(): ?string
    {
        return $this->tokenManager?->getToken('authenticate')->getValue();
    }
}