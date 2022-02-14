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
    private $path;

    /**
     * @var string|null
     */
    private $target;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct($config, RouterInterface $router)
    {
        $path = $config['logout_path'] ?? null;
        $this->path = trim($path, '/');
        $this->target = $config['logout_target'] ?? null;
        $this->router = $router;
    }

    /**
     * @param LogoutEvent $event
     * @return RedirectResponse
     */
    public function onLogout(LogoutEvent $event): RedirectResponse
    {
        $request = $event->getRequest();
        $target = empty($this->target) ? $request->getUri() : $request->getSchemeAndHttpHost() . $this->router->generate($this->target);
        $url = $request->getSchemeAndHttpHost() . $this->path . '?target' . $target;
        return new RedirectResponse($url);
    }
}