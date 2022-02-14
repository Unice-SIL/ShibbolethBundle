<?php

namespace UniceSIL\ShibbolethBundle\Security\Provider;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use UniceSIL\ShibbolethBundle\Security\Authenticator\ShibbolethAuthenticator;

abstract class AbstractShibbolethUserProvider implements UserProviderInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    /**
     * @return array
     */
    protected function getAttributes(): array {
        return $this->session->get(ShibbolethAuthenticator::SESSION_SHIBBOLETH_USER_ATTRIBUTES, []);
    }
}