# ShibbolethBundle
This is a Shibboleth bundle for Symfony 3+ that uses the Guard system.

## Installation
Install bundle via composer by running the following command :
```bash
composer require unicesil/shibboleth-bundle
```

If you don't use flex, enable the bundle in config/bundles.php :
```php
<?php

return [
    //...
    UniceSIL\ShibbolethBundle\UniceSILShibbolethBundle::class => ['all' => true]
];
```

Modify the file config/packages/unice_sil_shibboleth.yaml to add your shibboleth settings :
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
    enable_authenticator_manager: true
    
    provider:
      shibboleth:
        id: Your\Shibboleth\User\Provider\Class
    
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        shibboleth:
            lazy: true
            provider: shibboleth
            custom_authenticators:
              - unicesil.shibboleth_authenticator
            logout: ~

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

class User extends UserInterface
{
    //...

    public function getUserIdentifier() {
        // ...
    }
    
}
```

### UserProvider

```php

use UniceSIL\ShibbolethBundle\Security\Provider\AbstractShibbolethUserProvider;

class MyShibbolethUserProvider extends AbstractShibbolethUserProvider
{
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $shibbolethUserAttributes = $this->getAttributes();
        
        // Return an instance of User
    }
}
```
