<?php

namespace App\Controller;

use App\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_home', httpMethod: 'GET')]
    public function home(): string
    {
        $message = 'Page Home';

        return $this->twig->render('index/home.html.twig', [
            'message' => $message
        ]);
    }
    #[Route('/contact', name: 'app_contact', httpMethod: 'GET')]
    public function contact() : string
    {
        $message = 'Page Contact';

        return $this->twig->render('index/contact.html.twig', [
            'message' => $message
        ]);
    }
}