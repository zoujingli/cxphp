<?php

namespace app\index\controller;

use cxphp\core\App;
use cxphp\core\httpd\Request;

class Index
{
    public function index(Request $request)
    {
        return App::$object->view->fetch('text.html');
    }
}