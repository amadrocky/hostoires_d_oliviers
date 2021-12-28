<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class MailerService extends AbstractController
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     *
     * @param string $name
     * @param string $email
     * @param string $subject
     * @param string $template
     * @param string $token
     * @return void
     */
    public function sendEmail(string $name = null, string $email, string $subject, string $template, string $token = null, $addedVar = null)
    {
        $email = (new TemplatedEmail())
            ->from('histoiresdoliviers@gmail.com')
            ->to(new Address($email))
            ->subject($subject)

            // path of the Twig template to render
            ->htmlTemplate($template)

            // pass variables (name => value) to the template
            ->context([
                'subject' => $subject,
                'name' => $name,
                'token' => $token,
                'addedVar' => $addedVar
            ])
        ;

        $this->mailer->send($email);
    }
}