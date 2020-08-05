<?php

namespace app\index\controller;

use cxphp\core\App;
use cxphp\http\Request;

class Index
{
    public function index(Request $request)
    {
        echo '<pre>';
        print_r(App::$instance->db->name('SystemUser')->cache(true)->select()->toArray());
    }
}