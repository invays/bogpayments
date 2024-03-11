<?php
namespace Invays\BogPayments\Token;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Token
{
    private $client_id;
    private $client_secret;

    public function __construct(int $client_id, string $client_secret)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
    }

    public function getAccessToken()
    {
        $json = [];

        $client = new Client();

        try {
            $response = $client->post('https://oauth2.bog.ge/auth/realms/bog/protocol/openid-connect/token', [
                'headers'         => [
                    'Authorization' => 'Basic ' . base64_encode($this->client_id . ':' . $this->client_secret),
                    'Content-Type'  => 'application/x-www-form-urlencoded',
                ],
                'form_params'     => [
                    'grant_type' => 'client_credentials'
                ],
                'timeout'         => 30,
                'allow_redirects' => [
                    'max' => 10
                ],
                'http_errors'     => false // To handle non-2xx responses without throwing exceptions
            ]);

            if (!$json) {
                if ($response->getStatusCode() != 200) {
                    $json = json_decode($response->getBody()->getContents(), true);
                }
            }

            if ($response->getStatusCode() == 200) {
                $json = json_decode($response->getBody()->getContents(), true);
            }

        } catch (RequestException $e) {
            $json['error'] = "Guzzle Error #: " . $e->getCode() . ", Message: " . $e->getMessage();
        }

        return (object) $json;
    }

}
