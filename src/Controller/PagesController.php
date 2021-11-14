<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    /**
     * @Route("/conseils-entretien", name="advices")
     */
    public function advicesPage(): Response
    {
        return $this->render('pages/advices.html.twig');
    }

    /**
     * @Route("/livraison-plantation", name="delivery")
     */
    public function deliveryPage(): Response
    {
        return $this->render('pages/delivery.html.twig');
    }
}
