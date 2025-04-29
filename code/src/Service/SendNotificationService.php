<?php

namespace App\Service;

use App\Interface\TranslationServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class SendNotificationService {

    private HttpClientInterface $client;
    private TranslationServiceInterface $translator;


    public function __construct(HttpClientInterface $client , TranslationServiceInterface $translator)
    {
        $this->client = $client;
        $this->translator = $translator;

    }

    public function sendEmail(string $advice , string $description): void
    {
        $translatedFrench = $this->translator->translateToFrench($advice);

        $this->client->request('POST', 'http://ms-contact:8080/api/Contact/send-email', [
            'query' => [
                'recipientName' => 'Anass aouissi',
                'recipientEmail' => 'aouissianass@gmail.com',
                'subject' => 'Alerte météo : Préparez votre ferme face à ' . $description,
                'body' => 'Conseil : '.$translatedFrench,
            ],
        ]);
    }


    public function sendSms(string $advice , string $description): void
    {
        $translatedFrench = $this->translator->translateToFrench($advice);

        $this->client->request('POST', 'http://ms-contact:8080/api/Contact/send-sms', [
            'query' => [
                'phoneNumber' => '+212611378649',
                'message' => 'Alerte météo : Préparez votre ferme face à ' . $description . '. \\n Conseil : '.$translatedFrench,
            ],
        ]);
    }

}
