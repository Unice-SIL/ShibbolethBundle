# ShibbolethBundle
This is a Shibboleth bundle for Symfony 3+ that uses the Guard system.

## Installation
Install bundle via composer by running the following command :
```bash
composer require unicesil/shibboleth-bundle
```

Enable the bundle in app/AppKernel.php :
```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new UniceSIL\ShibbolethBundle\UniceSILShibbolethBundle(),
        // ...
    );
}
```

Modify your config.yml file to add the shibboleth settings :
```yaml
unice_sil_shibboleth:
    login_path: 'Shibboleth.sso/Login'  # The path used to call Shibboleth login authentication (default = 'Shibboleth.sso/Login')
    logout_path: 'Shibboleth.sso/Login'  # The path used to call Shibboleth logout (default = 'Shibboleth.sso/Logout')  
    username: 'eppn'  # The Shibboleth attribute that is used as username for the logged in user. The attribute must appear in the'attributes' parameter list (default = 'username')
    attributes: ['eppn', 'mail', 'givenName', 'sn']  # The list of attributes returned by Shibboleth Service Provider
    login_target : ''  # The route to which the user will be redirected after login. If this parameter is not filled, the user will be redirected to the page from which he comes. (default = null)
    logout_target : ''  # The route to which the user will be redirected after logout. If this parameter is not filled, the user will be redirected to the page from which he comes. (default = null)
```

And modify your security.yml file to secure your application :
```yaml
security:
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            anonymous: ~
            logout: ~
            guard:
              authenticators:
                - unicesil.shibboleth_authenticator

    access_control:
        - { path: ^/, roles: ROLE_USER }
  ```
  
Configure your application .htaccess or your apache configuration:
```
AuthType shibboleth
ShibRequestSetting requireSession 0
ShibUseHeaders On
ShibRequestSetting applicationId engagement
Require shibboleth
```

## User and UserProvider
Create your own User and UserProvider classes

### User
```php
namespace MyBundle\Security\User;

class User implements UserInterface
{
...
}
```

### UserProvider
```php
namespace MyBundle\Security\User;

use UniceSIL\ShibbolethBundle\Security\User\ShibbolethUserProviderInterface;

class MyShibbolethUserProvider extends ShibbolethUserProviderInterface
{
    public function loadUser(array $credentials)
    {
        $user = new User();
        $user->setMail($credentials['mail']);
        ...
        return $user;
    }
    
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }
}
```

Add your provider to the security.yml file
```php
security:
    providers:
        shibboleth:
            id: MyBundle\Security\User\MyShibbolethUserProvider
```
