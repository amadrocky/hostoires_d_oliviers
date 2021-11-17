<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductsRepository;


/**
 * @Route("/cart", name="cart_")
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
            'products' => $this->getcartProducts($request->getSession()->get('cartProductsIds')) ?? null
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
     * Get products objects
     *
     * @param [array|null] $sessionProducts
     * @return void
     */
    private function getcartProducts($sessionProducts)
    {
        $datas = null;

        if ($sessionProducts != null) {
            $datas = [];
            $i = 0;

            foreach ($sessionProducts as $key => $value) {
                $datas['product'.$i] = [
                        $this->productsRepository->find($key),
                        intval($value)
                    ];
                
                $i++;
            }
        }
        

        return $datas;
    }
}
