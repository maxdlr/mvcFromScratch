<?php

namespace App\Controller;

class IndexController extends AbstractController
{
    public function home(): string
    {
        $message = 'Page Home';

        return $this->twig->render('index/home.html.twig', [
            'message' => $message
        ]);
    }
    public function contact() : string
    {
        $message = 'Page Contact';

        return $this->twig->render('index/contact.html.twig', [
            'message' => $message
        ]);
    }
}