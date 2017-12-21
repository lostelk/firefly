<?php

namespace Core;

class Router extends \Phalcon\Mvc\Router
{
    public function getActionName()
    {
        $name = parent::getActionName();
        $name = str_replace(['_', '-'], ' ', $name);
        $name = ucwords($name);
        $name = lcfirst(str_replace(' ', '', $name));
        return $name;
    }
}
