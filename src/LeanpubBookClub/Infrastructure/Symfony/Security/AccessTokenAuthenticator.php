<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Security;

use Assert\Assert;
use LeanpubBookClub\Application\FlashType;
use LeanpubBookClub\Application\Members\Members;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AccessTokenAuthenticator extends AbstractGuardAuthenticator
{
    private Members $members;

    private SessionInterface $session;

    private TranslatorInterface $translator;

    private HttpUtils $httpUtils;

    public function __construct(
        Members $members,
        SessionInterface $session,
        TranslatorInterface $translator,
        HttpUtils $httpUtils
    ) {
        $this->members = $members;
        $this->session = $session;
        $this->translator = $translator;
        $this->httpUtils = $httpUtils;
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'login' && $request->query->has('token');
    }

    public function getCredentials(Request $request)
    {
        return $request->query->get('token');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if ($credentials === null) {
            return null;
        }

        Assert::that($credentials)->string('Expected the access token to be a string');

        return $this->members->getOneByAccessToken($credentials);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // If a user was loaded by its access token, we don't need to check anything else
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($this->session instanceof Session) {
            $this->session->getFlashBag()->add(
                FlashType::WARNING,
                $this->translator->trans('access_token_authentication_failed.flash_message')
            );
        }

        return $this->redirectToRoute($request, 'index');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?Response
    {
        return $this->redirectToRoute($request, 'member_area_index');
    }

    public function supportsRememberMe()
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return $this->redirectToRoute($request, 'index');
    }

    private function redirectToRoute(Request $request, string $route): Response
    {
        return $this->httpUtils->createRedirectResponse($request, $route);
    }
}
