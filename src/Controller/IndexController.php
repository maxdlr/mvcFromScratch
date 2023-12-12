<?php

namespace App\Controller;

class IndexController
{
    public function home(): string
    {
        echo 'page home';
    }
    public function contact() : string
    {
        echo 'page contact';
    }
}