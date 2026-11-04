<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Sends anonymous users to the login form, like form_login does.
 *
 * Symfony's ExceptionListener only remembers the target URL for safe, non-XHR
 * requests (a GET page view). When the session expires on an admin POST (form
 * save) or XHR, this entry point remembers the admin page the user came from
 * (Referer) instead, so they land back on it after logging in.
 */
final class LoginEntryPoint implements AuthenticationEntryPointInterface
{
    use TargetPathTrait;

    private const FIREWALL = 'main';
    private const LOGIN_ROUTE = 'app_login';

    public function __construct(private readonly HttpUtils $httpUtils)
    {
    }

    public function start(Request $request, ?AuthenticationException $authException = null): RedirectResponse
    {
        $targetAlreadySaved = $request->isMethodSafe() && !$request->isXmlHttpRequest();

        if (!$targetAlreadySaved && $request->hasSession()) {
            $adminUrl = $this->adminRefererUrl($request);
            if (null !== $adminUrl) {
                $this->saveTargetPath($request->getSession(), self::FIREWALL, $adminUrl);
            }
        }

        return $this->httpUtils->createRedirectResponse($request, self::LOGIN_ROUTE);
    }

    /**
     * Returns the Referer if it is an /admin page on this same host, null otherwise
     * (prevents redirecting to an external or arbitrary URL after login).
     */
    private function adminRefererUrl(Request $request): ?string
    {
        $referer = $request->headers->get('referer');
        if (!$referer) {
            return null;
        }

        $parts = parse_url($referer);
        if (false === $parts || ($parts['host'] ?? null) !== $request->getHost()) {
            return null;
        }

        $path = $parts['path'] ?? '';
        if ('/admin' !== $path && !str_starts_with($path, '/admin/')) {
            return null;
        }

        return $path.(isset($parts['query']) ? '?'.$parts['query'] : '');
    }
}
