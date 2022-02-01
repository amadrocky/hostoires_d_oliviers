<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    /**
     * @Route("/conseils-entretien", name="advices")
     *
     * @return Response
     */
    public function advicesPage(): Response
    {
        return $this->render('pages/advices.html.twig');
    }

    /**
     * @Route("/livraison-plantation", name="delivery")
     *
     * @return Response
     */
    public function deliveryPage(): Response
    {
        return $this->render('pages/delivery.html.twig');
    }

    /**
     * @Route("/mentions-legales", name="legal")
     *
     * @return Response
     */
    public function legalpage(): Response
    {
        return $this->render('pages/legal.html.twig');
    }

    /**
     * @Route("/CGU", name="CGU")
     *
     * @return Response
     */
    public function CGU(): Response
    {
        return $this->render('pages/CGU.html.twig');
    }
}
