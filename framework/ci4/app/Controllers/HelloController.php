<?php

namespace App\Controllers;

class HelloController extends BaseController
{
    public function index()
    {
        $data['helloWorldText'] = 'Hello World';
        return view('hello_view', $data);
    }
}