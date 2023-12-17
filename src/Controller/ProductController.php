<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Routing\Attribute\Route;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ProductController extends AbstractController
{
    /**
     * @throws OptimisticLockException
     * @throws SyntaxError
     * @throws ORMException
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/product/new', name: 'app_product_new', httpMethod: ['GET', 'POST'])]
    public function new(EntityManager $entityManager): string
    {
        if (isset($_POST['newProductBtn']) && $_POST['newProductBtn'] == 'Créer') {

            foreach ($_POST as $formField) {
                if ($formField === '') {
                    $this->redirect('/product/new-fail');
                }
            }

            if (isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] === UPLOAD_ERR_OK) {

                $originalFileName = $_FILES['uploadedFile']['name'];
                $fileTmpFileName = $_FILES['uploadedFile']['tmp_name'];
                $fileSize = $_FILES['uploadedFile']['size'];
                $fileNameCmps = explode(".", $originalFileName);
                $fileExtension = strtolower(end($fileNameCmps));

                $newFileName = md5(time() . $fileTmpFileName) . '.' . $fileExtension;
                $allowedfileExtensions = array('jpg', 'gif', 'png', 'zip', 'txt', 'xls', 'doc');

                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $uploadFileDir = __DIR__ . '/../../public/assets/images/';
                    $dest_path = $uploadFileDir . $newFileName;

                    move_uploaded_file($fileTmpFileName, $dest_path);
                    $product = new Product();
                    $product
                        ->setName($_POST['name'])
                        ->setDescription($_POST['description'])
                        ->setPrice($_POST['price'])
                        ->setFileName($newFileName)
                        ->setFileSize($fileSize);

                    $entityManager->persist($product);
                    $entityManager->flush();

                    $this->redirect('/product/new-success');
                } else {
                    $this->dd('Wrong file extension', $_FILES);
                }
            }
        }

        return $this->twig->render('product/new.html.twig', [
//            'product' => $product
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/product/list', name: 'app_product_list', httpMethod: ['GET'])]
    public function list(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        return $this->twig->render('product/list.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/product/new-success', name: 'app_product_new_success', httpMethod: ['GET', 'POST'])]
    public function newSuccess()
    {
        $successMessage = "Votre produit a été mis en ligne avec success";

        return $this->twig->render('product/new-success.html.twig', [
            'successMessage' => $successMessage
        ]);
    }

    #[Route('/product/new-fail', name: 'app_product_new_fail', httpMethod: ['GET', 'POST'])]
    public function newFail()
    {
        $failMessage = "Erreur de soumission de formulaire.";

        return $this->twig->render('product/new-fail.html.twig', [
            'failMessage' => $failMessage
        ]);
    }
}