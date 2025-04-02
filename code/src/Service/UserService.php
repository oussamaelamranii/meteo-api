<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserService
{
    private HttpClientInterface $client;
    private string $msUrl = "http://host.docker.internal:5075/api/User/export";

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }
    public function getUsers(){
        $response = $this->client->request('GET', $this->msUrl);
        return $response->toArray();
    }
}