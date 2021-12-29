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
        return $this->render('cart/index.html.twig', [
            'products' => $this->getCartProducts($request->getSession()->get('cartProducts')) ?? null
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
        $cartProducts = [];
        $id = $_POST['productId'];
        $quantity = $_POST['productQuantity'];

        if ($request->getSession()->get('cartProducts')) {
            $cartProducts = $request->getSession()->get('cartProducts');
        }
        
        $cartProducts[] = [
            'id' => $id, 
            'quantity' => $quantity
        ];

        $request->getSession()->set('cartProducts', $cartProducts);
        
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
        $cartProducts = $this->getCartProducts($request->getSession()->get('cartProducts'));
        
        if ($cartProducts != null && count($cartProducts) > 0) {
            $i = 0;

            foreach ($cartProducts as $value) {
                if ($value['product']->getId() == $id) {
                    unset($cartProducts[$i]);
                    $i++;
                }
            }

            $request->getSession()->set('cartProducts', $cartProducts);
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

            foreach ($sessionProducts as $value) {
                $datas[] = [
                        'product' => $this->productsRepository->find($value['id']),
                        'quantity' => intval($value['quantity'])
                    ];
                }
        }

        return $datas;
    }
}
