<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class MailerService extends AbstractController
{
    /**
     * Send an email via SendInBlue
     *
     * @param string $to
     * @param integer $templateId
     * @param array $params
     * @return void
     */
    public function sendInBlueEmail(string $to, int $templateId, array $params)
    {
        // Configure API key authorization: api-key
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $this->getParameter('sendinblue_api_key'));

        $apiInstance = new TransactionalEmailsApi(
            // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
            // This is optional, `GuzzleHttp\Client` will be used as default.
            new Client(),
            $config
        );
        $sendSmtpEmail = new SendSmtpEmail(); // \SendinBlue\Client\Model\SendSmtpEmail | Values to send a transactional email
        $sendSmtpEmail['to'] = [['email' => $to]];
        $sendSmtpEmail['templateId'] = $templateId;
        $sendSmtpEmail['params'] = $params;
        //$sendSmtpEmail['headers'] = ['X-Mailin-custom' => 'content-type:application/json|accept:application/json'];

        try {
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
        } catch (Exception $e) {
            echo 'Exception when calling TransactionalEmailsApi->sendTransacEmail: ', $e->getMessage(), PHP_EOL;
        }
    }
}