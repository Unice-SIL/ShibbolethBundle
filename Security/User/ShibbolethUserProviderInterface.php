<?php

namespace UniceSIL\ShibbolethBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Interface ShibbolethUserProviderInterface
 * @package UniceSIL\ShibbolethBundle\Security\User
 */
Interface ShibbolethUserProviderInterface extends UserProviderInterface{

    /**
     * @param array $credentials
     * @return mixed
     */
    public function loadUser(array $credentials);
}
