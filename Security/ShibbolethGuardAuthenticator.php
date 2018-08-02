<?php

namespace ShibbolethBundle\Security;


use ShibbolethBundle\Security\User\ShibbolethUserProviderInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ShibbolethGuardAuthenticator extends AbstractGuardAuthenticator
{

    private $config;
    private $router;
    private $session;
    private $tokenStorage;

    private $login_route;
    private $target;
    private $session_id;
    private $username;
    private $attributes;

    /**
     * ShibbolethGuardAuthenticator constructor.
     * @param $config
     * @param Router $router
     */
    public function __construct($config, Router $router, Session $session, TokenStorage $tokenStorage)
    {
        $this->config = $config;
        $this->router = $router;
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
        $this->login_route = $config['login_route'];
        $this->target = $config['target'];
        $this->session_id = $config['session_id'];
        $this->username = $config['username'];
        $this->attributes = $config['attributes'];
        if(!in_array($this->username, $this->attributes))
            throw new InvalidConfigurationException("Shibboleth configuration error : the value of username parameter must be in attributes list parameter");
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request){
        if (!empty($this->tokenStorage->getToken()) && $this->tokenStorage->getToken()->getUser()) {
            return false;
        }
        return true;
    }

    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $this->session->set('shibboleth.target', $request->getUri());
        return new RedirectResponse($this->router->generate($this->login_route));
    }

    /**
     * @param Request $request
     * @return array|null
     */
    public function getCredentials(Request $request)
    {
        if(empty($this->getAttribute($request, $this->session_id)))
            return null;
        $credentials = array();
        $credentials['username'] = $this->getAttribute($request, $this->username);
        foreach($this->attributes as $attribute){
            $credentials[$attribute] = $this->getAttribute($request, $attribute);
        }
        return $credentials;
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if(empty($credentials['username']))
            throw new UsernameNotFoundException("The username attribute is empty");
        if($userProvider instanceof ShibbolethUserProviderInterface)
            return $userProvider->loadUser($credentials);
        else if($userProvider instanceof  UserProviderInterface)
            return $userProvider->loadUserByUsername($credentials['username']);
        return null;

    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(array('message' => $exception->getMessageKey()), Response::HTTP_FORBIDDEN);
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if(!empty($this->target)) return new RedirectResponse($this->router->generate($this->target));
        return new RedirectResponse($this->session->get('shibboleth.target'));
    }

    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }

    /**
     * @param Request $request
     * @param $name
     * @return mixed
     */
    private function getAttribute(Request $request, $name){
        $attributes = array($name, strtoupper($name), "HTTP_".strtoupper($name), "REDIRECT_{$name}");
        foreach($attributes as $attribute)
            if(!empty($request->server->has($attribute))) return $request->server->get($attribute);
    }
}