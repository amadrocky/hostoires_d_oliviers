<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
     * @Route("/espace-client", name="account_")
     */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="index")
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $user = $this->getuser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('account/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/edition", name="edit", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function editInfos(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getuser();

        $user->setLastname($_POST['user_lastname']);
        $user->setFirstname($_POST['user_firstname']);
        $user->setEmail($_POST['user_email']);
        $user->setBirthdate($_POST['user_birthdate']);
        $user->setAdress($_POST['user_adress']);
        $user->setComplement($_POST['user_complement']);
        $user->setZipCode($_POST['user_zipcode']);
        $user->setCity($_POST['user_city']);
        $user->setModifiedAt(new \DateTime());
        
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Informations mises à jour');
        return $this->redirectToRoute('account_index');
    }
}
