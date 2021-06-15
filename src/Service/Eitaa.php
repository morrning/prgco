<?php
namespace App\Service;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class Eitaa
{
    private $client;
    private $token = 'bot17463:bc86cee4-d9a6-45d6-be44-1a925446d730';
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function sendToGroup($groupID,$body){
        $response = $this->client->request(
            'POST',
            'https://eitaayar.ir/api/' . $this->token . '/sendMessage',
            [
                'json' => ['chat_id' => $groupID, 'text' => $body]
            ]
        );
    }

    public function sendFileToGroup($groupID,$file){
        $data = [
            'chat_id' => $groupID,
            'file' => DataPart::fromPath($file),
        ];
        $formData = new FormDataPart($data);
        $response = $this->client->request('POST', 'https://eitaayar.ir/api/' . $this->token . '/sendFile', [
            'headers' => $formData->getPreparedHeaders()->toArray(),
            'body' => $formData->bodyToIterable(),
        ]);
        return $response;
    }

}