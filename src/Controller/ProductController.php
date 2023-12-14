<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;

class ProductController extends AbstractController
{
    public function new(EntityManager $entityManager): string
    {
        $product = new Product();
        $product->setName('ce produit');
        $product->setPrice(14);

        $entityManager->persist($product);
        $entityManager->flush();

        return $this->twig->render('product/new.html.twig', [
            'product' => $product
        ]);
    }

    public function list(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        return $this->twig->render('product/list.html.twig', [
            'products' => $products
        ]);
    }
}