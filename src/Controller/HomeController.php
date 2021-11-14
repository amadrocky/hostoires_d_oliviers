<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductsRepository;

class HomeController extends AbstractController
{
    /**
     *
     * @param ProductsRepository $productRepository
     */
    public function __construct(ProductsRepository $productRepository)
    {
        $this->productsRepository = $productRepository;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $lastProducts = $this->productsRepository->getLastThree();

        return $this->render('home/index.html.twig', [
            'lastProducts' => $lastProducts
        ]);
    }
}
