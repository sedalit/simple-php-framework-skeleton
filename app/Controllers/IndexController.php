<?php

namespace App\Controllers;

class IndexController extends BaseController {

    public function index() : mixed
    {
        return $this->render('index');
    }
}