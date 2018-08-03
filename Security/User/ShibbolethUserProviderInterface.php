<?php

namespace UniceSIL\ShibbolethBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;

Interface ShibbolethUserProviderInterface extends UserProviderInterface{
    public function loadUser(array $credentials);
}