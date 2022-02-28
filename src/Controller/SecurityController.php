<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Form\UserType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\MailerService;
class SecurityController extends AbstractController
{
    /**
     * @var MailerService
     */
    private $mailer;

    /**
     *
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(MailerService $mailer, UserRepository $userRepository) 
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }

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

            $this->mailer->sendInBlueEmail(
                $user->getEmail(),
                5,
                [
                    'NOM' => $user->getFirstName(),
                ]
            );

            $this->addFlash('success', 'Votre compte utilisateur a bien été créé.');

            return $this->redirectToRoute('home');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
        ]);
    }

    /**
     * Send a mail for modify password
     * 
     * @Route("/recover", name="recover", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function forgotPassword(Request $request): Response
    {
        $email = $request->request->get('email');
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if ($user) {
            $em = $this->getDoctrine()->getManager();
            $user->setToken($this->generateToken());
            $em->persist($user);
            $em->flush();

            $this->mailer->sendInBlueEmail(
                $user->getEmail(),
                6,
                [
                    'PRENOM' => $user->getFirstname(),
                    'TOKEN' => $user->getToken()
                ]
            );
        }

        $this->addFlash('success', 'Un email a été envoyé à votre adresse.');

        return $this->redirectToRoute('app_login');
    }

    /**
     * Create a new password
     * 
     * @Route("/reset-password/{token}", name="reset_password", methods={"GET","POST"})
     * 
     * @param Request $request
     * @param string $token
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->userRepository->findOneBy(['token' => $token]);

        if ($user) {
            $form = $this->createForm(ResetPasswordType::class, $user);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $user->setPassword($passwordEncoder->encodePassword(
                    $user,
                    $user->getPassword()
                ));
                $user->setModifiedAt(new \DateTime());
                $user->setToken(null);
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Votre mot de passe à bien été modifié.');

                return $this->redirectToRoute('home');
            }

            return $this->render('security/resetPassword.html.twig', [
                'form' => $form->createView(),
                'user' => $this->getUser()
            ]);
        } else {
            return $this->redirectToRoute('home');
        }
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
