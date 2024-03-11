<?php
namespace Invays\BogPayments\Order;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Invays\BogPayments\BogPayments;


//Optional
use Invays\BogPayments\Theme\Theme;

class Refund extends BogPayments
{
    private $access_token;

    public function __construct(string $access_token)
    {
        $this->access_token = $access_token;
    }


    /**
     * create_refund: https://api.bog.ge/docs/en/payments/refund
     *
     * @param  mixed $order_id
     * @param  mixed $amount
     * @return array
     */
    public function create_refund(string $order_id, float $amount): array
    {

        $json = [];

        if (!isset($this->access_token) || is_null($this->access_token) || empty($this->access_token)) {
            $json = 'Access token is required';
        }

        if (!$json) {

            $refund_data['amount'] = $amount;

            $client = new Client();

            try {
                $headers = [
                    'Authorization' => 'Bearer ' . $this->access_token,
                    'Content-Type'  => 'application/json',
                ];

                $response = $client->post("https://api.bog.ge/payments/v1/payment/refund/{$order_id}", [
                    'headers' => $headers,
                    'json'    => $refund_data,
                ]);


                if ($response->getStatusCode() != 200) {
                    $json = json_decode($response->getBody()->getContents(), true);
                }

                if (!$json) {
                    if ($response->getStatusCode() == 200) {
                        $json = json_decode($response->getBody()->getContents(), true);
                    }
                }

            } catch (RequestException $e) {
                $json['error'] = "Guzzle Error #: " . $e->getCode() . ", Message: " . $e->getMessage();
            }


        }

        return $json;
    }

}
