<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductsRepository;


/**
 * @Route("/panier", name="cart_")
 */
class CartController extends AbstractController
{

    /** @var ProductsRepository */
    private $productsRepository;

    public function __construct(ProductsRepository $productsRepository) {
        $this->productsRepository = $productsRepository;
    }

    /**
     * @Route("/", name="index")
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        //$request->getSession()->clear();
        return $this->render('cart/index.html.twig', [
            'products' => $this->getCartProducts($request->getSession()->get('cartProductsIds')) ?? null
        ]);
    }

    /**
     * Add to cart action
     * 
     * @Route("/add", name="add", methods={"POST"})
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function addToCart(Request $request): RedirectResponse
    {
        $cartProductsIds = [];
        $id = $_POST['productId'];
        $quantity = $_POST['productQuantity'];

        if ($request->getSession()->get('cartProductsIds')) {
            $cartProductsIds = $request->getSession()->get('cartProductsIds');
        }
        
        $cartProductsIds[$id] = $quantity;

        $request->getSession()->set('cartProductsIds', $cartProductsIds);

        $this->addFlash('succes', 'Produit ajouté au panier');

        return $this->redirectToRoute('products_index');
    }

    /**
     * @Route("/suppression/{id}", name="remove", methods={"GET"})
     *
     * @param Request $request
     * @param integer $id
     * @return RedirectResponse
     */
    public function removeToCart(Request $request, int $id): RedirectResponse
    {
        $cartProductsIds = $this->getCartProducts($request->getSession()->get('cartProductsIds'));

        if ($cartProductsIds != null && count($cartProductsIds) > 0) {
            $i = 0;
            foreach ($cartProductsIds as $value) {
                if ($value[0]->getId() == $id) {
                    unset($cartProductsIds[$i]);
                    $i++;
                }
            }

            $request->getSession()->set('cartProductsIds', $cartProductsIds);
        }

        $this->addFlash('succes', 'Produit retiré du panier');

        return $this->redirectToRoute('cart_index');
    }

    /**
     * Get products objects
     *
     * @param [array|null] $sessionProducts
     * @return void
     */
    private function getCartProducts($sessionProducts)
    {
        $datas = null;

        if ($sessionProducts != null) {
            $datas = [];
            $i = 0;

            foreach ($sessionProducts as $key => $value) {
                $datas[] = [
                        $this->productsRepository->find($key),
                        intval($value)
                    ];
                
                $i++;
            }
        }
        

        return $datas;
    }
}
