<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MailerService;

/**
 * @Route("/contact")
 */
class ContactController extends AbstractController
{

    private $mailer;

    public function __construct(
        ContactRepository $contactRepository, 
        MailerService $mailer
    )
    {
        $this->contactRepository = $contactRepository;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/", name="contact_index", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $contact->setCreatedAt(new \DateTime());
            $contact->setUpdatedAt(new \DateTime());
            $contact->setWorkflowState('active');
            $entityManager->persist($contact);
            $entityManager->flush();

            $this->mailer->sendInBlueEmail(
                'histoiresdoliviers@gmail.com',
                1,
                [
                    'OBJET' => $form->getData()->getObject()->getName(),
                    'FROM' => $form->getData()->getEmail(),
                    'NOM' => $form->getData()->getName(),
                    'MESSAGE' => $form->getData()->getMessage()
                ]
            );

            $this->addFlash('success', 'Votre message à bien été envoyé');

            return $this->redirectToRoute('contact_index');
        }

        return $this->render('contact/index.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }

}
