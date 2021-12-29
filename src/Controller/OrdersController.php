<?php

namespace App\Controller;

use App\Entity\Orders;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductsRepository;
use Stripe\Checkout\Session;
use Stripe\SetupIntent;
use Stripe\Stripe;

/**
 * @Route("/commande", name="orders_")
 */
class OrdersController extends AbstractController
{
    /**
     * @var OrdersRepository
     */
    private $productsRepository;

    private const DELIVERY = 8000;
    private const PLANTING = 13000;

    public function __construct(ProductsRepository $productsRepository) 
    {
        $this->productsRepository = $productsRepository;
    }

    /**
     * @Route("/creation", name="new", methods={"POST"})
     */
    public function new(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $order = new Orders();
        $date = new \DateTime();
        
        $order->setNumber(strtoupper(uniqId(rand())));

        $products = $request->getSession()->get('cartProducts');
        $orderItems = [];
        $orderAmount = $_POST['amount'];

        foreach($products as $product) {
            $orderItems[] = $this->productsRepository->find(intval($product['id']));
        }

        foreach($orderItems as $orderItem) {
            $order->addProduct($orderItem);
        }
        
        $order->setAmount(intval($orderAmount));
        
        $order->setCreatedAt($date);
        $order->setModifiedAt($date);
        $order->setWorkflowState('created');

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->redirectToRoute('orders_show', ['number' => $order->getNumber()]);
    }

    /**
     * @Route("/{number}", name="show", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Orders $order
     * @return Response
     */
    public function show(Request $request, Orders $order): Response
    {
        $taxAmount = $this->getTaxAmount($request);

        if ($request->isMethod('post')) {
            $entityManager = $this->getDoctrine()->getManager();
            $date = new \DateTime();
            $amount = intval($_POST['amount']);

            if ($_POST['delivery'] == "1") {
                $amount = $amount + self::DELIVERY;
                $order->setIsDelivery(true);
            }

            if ($_POST['delivery'] == "2") {
                $amount = $amount + self::PLANTING;
                $order->setIsDelivery(true);
            }

            $order->setAmount($amount);
            $order->setModifiedAt($date);
            $order->setWorkflowState('filled');

            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('orders_address', ['number' => $order->getNumber()]);
        }

        return $this->render('orders/show.html.twig', [
            'order' => $order,
            'taxAmount' => $taxAmount,
            'products' => $this->getCartProducts($request->getSession()->get('cartProducts'))
        ]);
    }

    /**
     * @Route("/{number}/adresse", name="address", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Orders $order
     * @return Response
     */
    public function addAddress(Request $request, Orders $order): Response
    {
        if ($request->isMethod('post')) {
            $entityManager = $this->getDoctrine()->getManager();
            $date = new \DateTime();

            $order->setName($_POST['user_name']);
            $order->setEmail($_POST['user_email']);
            $order->setWorkflowState('addressed');
            $order->setBillingAddress($_POST['user_billing_address']);
            $order->setBillingComplement($_POST['user_billing_complement']);
            $order->setBillingZipCode($_POST['user_billing_zip_code']);
            $order->setBillingCity($_POST['user_billing_city']);

            if ($_POST['user_phone_number']) {
                $order->setPhoneNumber($_POST['user_phone_number']);
            }

            if (isset($_POST['user_address'])) {
                $order->setAddress($_POST['user_address']);
                $order->setComplement($_POST['user_complement']);
                $order->setZipCode($_POST['user_zip_code']);
                $order->setCity($_POST['user_city']);
            }

            $entityManager->persist($order);
            $entityManager->flush();

            // Stripe checkout
            $stripe = new \Stripe\StripeClient($this->getParameter('stripe_api_key'));
            $customer = $stripe->customers->create([
                'email' => $order->getEmail(),
            ]);

            Stripe::setApiKey($this->getParameter('stripe_api_key'));
            $parameters = [
                'customer' => $customer,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Commande '.$order->getNumber(),
                        ],
                        'unit_amount' => $order->getAmount(),
                        'product_data' => [
                            'name' => 'Commande '.$order->getNumber(),
                            'images' => ["https://bends-community.fr/build/images/Webp.net-resizeimage.png"],
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => 'https://histoiresdoliviers.fr',
                'cancel_url' => 'https://histoiresdoliviers.fr',
            ];
            $session = Session::create($parameters);

            $this->container->get('session')->set('setup_intent', $session->setup_intent);

            return $this->redirect($session->url, Response::HTTP_SEE_OTHER);
            //$intent = SetupIntent::retrieve($setupIntentId);

            //return $this->render('orders/recap.html.twig', ['order' => $order]);
        }

        return $this->render('orders/address.html.twig', ['order' => $order]);
    }

    /**
     *
     * @param Request $request
     * @param Orders $order
     * @return integer
     */
    private function getTaxAmount(Request $request): int
    {
        $amount = null;

        foreach($this->getCartProducts($request->getSession()->get('cartProducts')) as $product) {
            $productTax = round($product['product']->getPrice() / 100 * $product['product']->getTax()->getValue());
            $amount += $productTax;
        }

        return $amount;
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