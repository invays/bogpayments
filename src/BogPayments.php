<?php
namespace Invays\BogPayments;

use Invays\BogPayments\Token\Token;
use Invays\BogPayments\Order\Order;
use Invays\BogPayments\Order\Refund;

class BogPayments
{
    private $token;
    public $order;
    public $refund;

    public function __construct(int $client_id, string $client_secret)
    {
        try {

            $generate_token = new Token($client_id, $client_secret);
            $this->token = $generate_token->getAccessToken();

            if (!isset($this->token->access_token) || is_null($this->token->access_token) || empty($this->token->access_token)) {
                throw new \Exception($this->token->error);
            }

            $this->order = new Order($this->token->access_token);
            $this->refund = new Refund($this->token->access_token);

        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }


}
