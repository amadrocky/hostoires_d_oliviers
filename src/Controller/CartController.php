<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cart", name="cart_")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
        
        ]);
    }

    /**
     * Add to cart action
     * 
     * @Route("/add", name="add", methods={"POST"})
     *
     * @param Request $request
     * @param integer $id
     * @return void
     */
    public function addToCart(Request $request)
    {
        $cartProductsIds = [];
        $id = $_POST['productId'];

        if ($request->getSession()->get('cartProductsIds')) {
            $cartProductsIds = $request->getSession()->get('cartProductsIds');
        }
        
        $cartProductsIds[] = $id;

        $request->getSession()->set('cartProductsIds', $cartProductsIds);

        $this->addFlash('succes', 'Produit ajouté au panier');

        return $this->redirectToRoute('products_index');
    }
}
