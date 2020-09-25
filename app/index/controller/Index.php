<?php

namespace app\index\controller;

use cxphp\httpd\Request;

class Index
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {

        echo '<pre>';
        dump(number_format(memory_get_usage()));
        dump(var_export(get_declared_classes(), true));
        echo '</pre>';
        // return App::$object->view->fetch('text.html');
    }
}