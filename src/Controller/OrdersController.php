<?php

namespace App\Controller;

use App\Entity\Orders;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductsRepository;

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
        
        $order->setNumber(uniqid());

        $products = $_POST['products'];
        $orderItems = [];
        $orderAmount = $_POST['amount'];

        foreach($products as $product) {
            $orderItems[] = $this->productsRepository->find(intval($product));
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

        return $this->redirectToRoute('orders_show', ['id' => $order->getId()]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Orders $order
     * @return Response
     */
    public function show(Request $request, Orders $order): Response
    {
        if ($request->isMethod('post')) {
            $entityManager = $this->getDoctrine()->getManager();
            
            $order->setUser($_POST['user_email']);
            $order->setModifiedAt($date);
            $order->setWorkflowState('filled');
            $order->setBillingAddress($_POST['user_address']);
            $order->setBillingComplement($_POST['user_complement']);
            $order->setBillingZipCode($_POST['user_zip_code']);
            $order->setBillingCity($_POST['user_city']);

            if ($_POST['user_phone_number']) {
                $order->setPhoneNumber($_POST['user_phoneNumber']);
            }

            if ($_POST['delivery']) {
                $order->setAddress($_POST['user_address']);
                $order->setComplement($_POST['user_complement']);
                $order->setZipCode($_POST['user_zip_code']);
                $order->setCity($_POST['user_city']);
            }

            // ajouter le calcul du montant
            $order->setAmount();

            $entityManager->persist($order);
            $entityManager->flush();

            return $this->render('orders/recap.html.twig', ['order' => $order]);
        }

        return $this->render('orders/show.html.twig', ['order' => $order]);
    }
}