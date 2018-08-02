<?php

namespace ShibbolethBundle\Services;


use Symfony\Bundle\FrameworkBundle\Routing\Router;

class RouterService
{

    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }
    public function getRouter(){
        return $this->router;
    }
}