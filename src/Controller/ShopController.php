<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/boutique", name="shop_")
 */
class ShopController extends AbstractController
{

    /**
     * @Route("/", name="index")
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('shop/index.html.twig', [
            
        ]);
    }
}
