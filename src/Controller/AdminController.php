<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Products;
use App\Form\ProductsType;
use App\Repository\ProductsRepository;
use App\Repository\OrdersRepository;
use Endroid\QrCode\QrCode;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/produits", name="products", methods={"GET"})
     *
     * @param ProductsRepository $productsRepository
     * @return Response
     */
    public function productsIndex(ProductsRepository $productsRepository): Response
    {
        return $this->render('admin/products/index.html.twig', [
            'products' => $productsRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    /**
     * @Route("/produit/{id}", name="products_show", methods={"GET"})
     *
     * @param Products $product
     * @return Response
     */
    public function productsShow(Products $product): Response
    {
        return $this->render('admin/products/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/produits/ajouter", name="products_add", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function addProduct(Request $request): Response
    {
        $product = new Products();
        $form = $this->createForm(ProductsType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            /* Récupération des images */
            $uploadDir = '/var/www/histoiresdoliviers.fr/hostoires_d_oliviers/public/images/products/';
            $files = [];

            for ($i = 1; $i <= 3; $i++) {
                if (isset($_FILES['img' . $i])) {
                    $fileName = $_FILES['img' . $i]['name'];

                    if ($fileName !== "") {
                        /* On renomme l'image */
                        $extention = strrchr($fileName, ".");
                        $fileName = 'product_image_' . uniqid() . $extention;

                        $uploadFile = $uploadDir . basename($fileName);
                        move_uploaded_file($_FILES['img' . $i]['tmp_name'], $uploadFile);
                        $files[] = basename($uploadFile);
                    }
                }
            }

            $product->setPictures($files);

            // Create a basic QR code
            $qrCode = new QrCode('https://www.histoiresdoliviers.fr/boutique/produit/' . $product->getId());
            $qrCode->setSize(300);

            // Set advanced options
            $qrCode->setWriterByName('png');
            $qrCode->setEncoding('UTF-8');
            $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0]);
            $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255]);
            $qrCode->setValidateResult(false);

            // name qrCode
            $qrCodeName = uniqId() . '.png';

            // Save it to a file
            $qrCode->writeFile('/var/www/histoiresdoliviers.fr/hostoires_d_oliviers/public/images/qrCodes/'. $qrCodeName);

            $product->setQrCode($qrCodeName);

            $product->setCreatedAt(new \DateTime());
            $product->setModifiedAt(new \DateTime());
            $product->setWorkflowState('active');

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/products/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/produit/{id}/edit", name="products_edit", methods={"GET", "POST"})
     *
     * @param Products $product
     * @return Response
     */
    public function editProduct(Request $request, Products $product): Response
    {
        $form = $this->createForm(ProductsType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $uploadDir = '/var/www/histoiresdoliviers.fr/hostoires_d_oliviers/public/images/products/';
            $files = [];

            for ($i = 1; $i <= 3; $i++) {
                if (isset($_FILES['img' . $i])) {
                    $fileName = $_FILES['img' . $i]['name'];

                    if ($fileName !== "") {
                        /* On renomme l'image */
                        $extention = strrchr($fileName, ".");
                        $fileName = 'product_image_' . uniqid() . $extention;

                        $uploadFile = $uploadDir . basename($fileName);
                        move_uploaded_file($_FILES['img' . $i]['tmp_name'], $uploadFile);
                        $files[] = basename($uploadFile);
                    }
                }
            }

            if (count($files) > 0 && count($product->getPictures()) > 0) {
                foreach ($product->getPictures() as $picture) {
                    // Supprime le fichier image stocké
                    unlink($uploadDir . $picture);
                }

                $product->setPictures($files);
            }

            $product->setModifiedAt(new \DateTime());

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('admin_products_show', ['id' => $product->getId()]);
        }

        return $this->render('admin/products/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }

    /**
     * @Route("/commandes", name="orders", methods={"GET"})
     *
     * @param OrdersRepository $ordersRepository
     * @return Response
     */
    public function ordersIndex(OrdersRepository $ordersRepository): Response
    {
        return $this->render('admin/orders/index.html.twig', [
            'orders' => $ordersRepository->findBy(['workflowState' => 'paid'], ['modifiedAt' => 'DESC'])
        ]);
    }
}
