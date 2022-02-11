<?php

namespace UniceSIL\ShibbolethBundle\Security\Authenticator;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class ShibbolethAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    public const SESSION_SHIBBOLETH_USER_ATTRIBUTES = 'shibboleth.user_attributes';

    /**
     * @var Router
     */
    private $router;

    /**
     * @var string
     */
    private $loginPath;

    /**
     * @var string
     */
    private $loginTarget;

    /**
     * @var string
     */
    private $sessionId;

    /**
     * @var string
     */
    private $username;

    /**
     * @var array
     */
    private $attributes;

    /**
     * ShibbolethGuardAuthenticator constructor.
     * @param array $config
     * @param Router $router
     */
    public function __construct(array $config, Router $router)
    {
        $this->router = $router;
        $this->loginPath = $config['login_path'];
        $this->loginTarget = $config['login_target'];
        $this->sessionId = $config['session_id'];
        $this->username = $config['username'];
        $this->attributes = $config['attributes'];
        if (!in_array($this->username, $this->attributes)) {
            throw new InvalidConfigurationException(
                'Shibboleth configuration error : the value of username parameter must be in attributes list parameter'
            );
        }
    }

    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse(
            $request->getSchemeAndHttpHost() . '/' .
            trim($this->loginPath, '/') . '?target=' .
            (empty($this->loginTarget) ? $request->getUri() : $request->getSchemeAndHttpHost() . $this->router->generate($this->loginTarget))
        );
    }

    /**
     * @param Request $request
     * @return bool|null
     */
    public function supports(Request $request): ?bool
    {
        return !empty($this->getAttribute($request, $this->sessionId));
    }

    /**
     * @param Request $request
     * @return SelfValidatingPassport
     */
    public function authenticate(Request $request): SelfValidatingPassport
    {
        $username = $this->getAttribute($request, $this->username);

        if (empty($username)) {
            throw new CustomUserMessageAuthenticationException('Shibboleth authentication failed');
        }

        $attributes = [];
        foreach ($this->attributes as $attribute){
            $attributes[$attribute] = $this->getAttribute($request, $attribute);
        }

        $request->getSession()->set(self::SESSION_SHIBBOLETH_USER_ATTRIBUTES, $attributes);
        return new SelfValidatingPassport(
            new UserBadge($username)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }

    /**
     * @param Request $request
     * @param $name
     * @return mixed|null
     */
    private function getAttribute(Request $request, $name){
        $attributes = [$name, strtoupper($name), 'HTTP_' . strtoupper($name), 'REDIRECT_' . $name];
        foreach ($attributes as $attribute) {
            if (!empty($request->server->has($attribute))) {
                return $request->server->get($attribute);
            }
        }
        return null;
    }

}