<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Routing\Attribute\Route;
use Doctrine\ORM\EntityManager;

class ProductController extends AbstractController
{
    #[Route('/product/list', name: 'app_product_list', httpMethod: ['GET', 'POST'])]
    public function list(ProductRepository $productRepository, EntityManager $entityManager): string
    {
        $products = $productRepository->findAll();
        $messages = [];

        if (isset($_POST['newProductBtn']) && $_POST['newProductBtn'] == 'Créer') {

            foreach ($_POST as $formField) {
                if ($formField === '') {
                    $this->redirect('/product/new-fail');
                }
            }

            if (isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] === UPLOAD_ERR_OK) {

                $originalFileName = $_FILES['uploadedFile']['name'];
                $tmpFileName = $_FILES['uploadedFile']['tmp_name'];

                $fileNameCmps = explode(".", $originalFileName);
                $fileExtension = strtolower(end($fileNameCmps));

                $fileSize = $_FILES['uploadedFile']['size'];

                $newFileName = md5(time() . $tmpFileName) . '.' . $fileExtension;
                $allowedfileExtensions = array('jpg', 'gif', 'png', 'zip', 'txt', 'xls', 'doc');

                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $uploadFileDir = __DIR__ . '/../../public/assets/images/';
                    $dest_path = $uploadFileDir . $newFileName;

                    move_uploaded_file($tmpFileName, $dest_path);

                    $product = new Product();
                    $product
                        ->setName($_POST['name'])
                        ->setDescription($_POST['description'])
                        ->setPrice($_POST['price'])
                        ->setFileName($newFileName)
                        ->setFileSize($fileSize);

                    $entityManager->persist($product);
                    $entityManager->flush();

                    $messages[] = ['type' => 'Succes !', 'content' => 'Produit ajouté avec brio'];

                } else {
                    $messages[] = ['type' => 'Echec...', 'content' => 'Mauvaise extension de fichier.'];
                }
            }
        }

        return $this->twig->render('product/list.html.twig', [
            'products' => $products,
            'messages' => $messages,
        ]);
    }
}