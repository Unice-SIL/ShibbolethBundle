<?php

namespace UniceSIL\ShibbolethBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutEventListener
{
    /**
     * @var string|null
     */
    private ?string $path;

    /**
     * @var string|null
     */
    private ?string $target;

    /**
     * @var RouterInterface
     */
    private RouterInterface $router;

    public function __construct($config, RouterInterface $router)
    {
        $path = $config['logout_path'] ?? null;
        $this->path = trim($path, '/');
        $this->target = $config['logout_target'] ?? null;
        $this->router = $router;
    }

    /**
     * @param LogoutEvent $event
     */
    public function onLogout(LogoutEvent $event)
    {
        $request = $event->getRequest();
        $target = empty($this->target) ? $request->getUri() : $request->getSchemeAndHttpHost() . $this->router->generate($this->target);
        $url = $request->getSchemeAndHttpHost() . '/' . $this->path . '?target=' . $target;
        $event->setResponse(new RedirectResponse($url));
    }
}