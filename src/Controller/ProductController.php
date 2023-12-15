<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Routing\Attribute\Route;
use Doctrine\ORM\EntityManager;

class ProductController extends AbstractController
{
    #[Route('/product/new', name: 'app_product_new', httpMethod: 'GET')]
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
    #[Route('/product/list', name: 'app_product_list', httpMethod: 'GET')]
    public function list(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        return $this->twig->render('product/list.html.twig', [
            'products' => $products
        ]);
    }
}