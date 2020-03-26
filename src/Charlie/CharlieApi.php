<?php

declare(strict_types=1);

namespace Robwasripped\Charliewfh\Charlie;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class CharlieApi
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function login(string $user, string $password)
    {
        // get login form
        $response = $this->client->request('GET', 'login');
        $csrf = $this->extractCSRFToken($response);

        // Submit login form
        $this->client->request('POST')
    }

    public function submitWFH(\DateTimeImmutable $date)
    {
        $this->client->request('POST', 'remote_days');
    }

    private function extractCSRFToken(ResponseInterface $response): string
    {
        $body = $response->getBody()->getContents();

        $xml = new \SimpleXMLElement($body);
        $results = $xml->xpath("//meta[@name='csrf-token']");

        return $results[0]['content'];
    }
}
