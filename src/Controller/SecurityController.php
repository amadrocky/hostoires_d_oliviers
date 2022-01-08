<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
class SecurityController extends AbstractController
{
   /**
    * @Route("/login", name="app_login")
    *
    * @param AuthenticationUtils $authenticationUtils
    * @return Response
    */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 'error' => $error
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('account_index');
        }

        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            ));
            $user->setFirstname(ucfirst(strtolower($user->getFirstname())));
            $user->setLastname(ucfirst(strtolower($user->getLastname())));
            $user->setCreatedAt(new \DateTime());
            $user->setModifiedAt(new \DateTime());
            $user->setWorkflowState('created');
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Votre compte utilisateur a bien été créé.');

            return $this->redirectToRoute('home');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Token generation
     *
     * @return string
     */
    private function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
