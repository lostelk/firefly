<?php

namespace FireFly\Controllers;

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        echo 'Hello, Kitty';
    }

    public function notFoundAction()
    {
        $app = new \Phalcon\Mvc\Application();
        $app->response->setStatusCode(404, "Not Found")->sendHeaders();
        echo 'This is crazy, but this page was not found!';
    }
}
