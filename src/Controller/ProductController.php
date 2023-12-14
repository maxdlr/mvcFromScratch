<?php

namespace App\Controller;

use App\Entity\Product;
class ProductController extends AbstractController
{
    public function list(): string
    {
        $product = new Product();
        $product->setName('ce produit');
        $product->setPrice(14);

        return $this->twig->render('product/list.html.twig', [
            'product' => $product
        ]);
    }
}