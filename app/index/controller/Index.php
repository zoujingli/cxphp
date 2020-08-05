<?php

namespace app\index\controller;

use cxphp\http\Request;

class Index
{
    public function index(Request $request)
    {
        echo '<pre>';
        dump($request->get());
        dump($request->path());
        echo '</pre>';
        echo '<script>setInterval(function(){
    location.reload();
},100)</script>';
    }
}