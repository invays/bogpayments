<?php
namespace Invays\BogPayments\Order;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Invays\BogPayments\BogPayments;
use Invays\BogPayments\Order\Language;
use Invays\BogPayments\Order\Currency;

//Optional
use Invays\BogPayments\Theme\Theme;

class Order extends BogPayments
{
    private $access_token;
    private $language;
    private $currency;
    private $callback_url;
    private $failure_url;
    private $success_url;
    private $security_url_code;
    private $theme;

    public function __construct(string $access_token)
    {
        $this->access_token = $access_token;
    }

    public function setLanguage(string $language = 'en')
    {
        $this->language = (!in_array($language, Language::LANGUAGES)) ? 'en' : $language;
    }

    public function setCurrency(string $currency = 'USD')
    {
        $this->currency = (!in_array($currency, Currency::CURRENCIES)) ? 'USD' : $currency;
    }

    public function setSecurityUrlCode(string $security_url_code)
    {
        $this->currency = $security_url_code;
    }

    public function setCallbackUrl(string $callback_url)
    {
        $this->callback_url = $callback_url;
    }

    public function setFailureUrl(string $failure_url)
    {
        $this->failure_url = $failure_url;
    }

    public function setSuccessUrl(string $success_url)
    {
        $this->success_url = $success_url;
    }

    public function setHeaderTheme(string $theme)
    {
        $this->theme = (!in_array($theme, Theme::THEMES)) ? 'dark' : $theme;
    }

    /**
     * create_order: https://api.bog.ge/docs/en/payments/standard-process/create-order
     *
     * @param  mixed $order_info
     * @return array
     */
    public function create_order(array $order_info): array
    {
        $json = [];

        if (!isset($this->access_token) || is_null($this->access_token) || empty($this->access_token)) {
            $json = 'Access token is required';
        }

        if (empty($this->callback_url)) {
            $json['error'] = 'Callback URL is required';
        }

        if (!isset($order_info["shop_order_id"]) || empty($order_info["shop_order_id"])) {
            $json['error'] = 'Shop order ID is required';
        }

        if (!isset($order_info["products"]) || empty($order_info["products"])) {
            $json['error'] = 'Cart items are required';
        }

        if (!$json) {

            $order_data = [];

            $order_data["callback_url"] = $this->callback_url;
            $order_data["external_order_id"] = $order_info["shop_order_id"];

            // Optional Customer info
            if (isset($order_info["customer"]) && !empty($order_info["customer"])) {
                if (isset($order_info["customer"]["full_name"]) && !empty($order_info["customer"]["full_name"])) {
                    $order_data['buyer']['full_name'] = $order_info["customer"]["full_name"];
                }

                if (isset($order_info["customer"]["masked_email"]) && !empty($order_info["customer"]["masked_email"])) {
                    $order_data['buyer']['masked_email'] = $order_info["customer"]["masked_email"];
                }

                if (isset($order_info["customer"]["masked_phone"]) && !empty($order_info["customer"]["masked_phone"])) {
                    $order_data['buyer']["masked_phone"] = $order_info["customer"]["masked_phone"];
                }
            }

            // External Option for USD or EUR
            //$order_data['payment_method'] = 'bog_p2p';

            $order_data['purchase_units'] = [];
            $order_data['purchase_units']['currency'] = $this->currency;

            $total_amount = 0;

            $basket = array_map(function ($product) use (&$total_amount, &$total_price) {
                $basket_item = [
                    "quantity"   => $product["quantity"],
                    "unit_price" => $product["price"],
                    "product_id" => $product["product_id"]
                ];

                $total_amount = $total_amount + ($product["quantity"] * $product["price"]);

                if (isset ($product["description"]) && !empty ($product["description"])) {
                    $basket_item["description"] = $product["description"];
                }

                if (isset ($product["unit_discount_price"]) && !empty ($product["unit_discount_price"])) {
                    $basket_item["unit_discount_price"] = $product["unit_discount_price"];
                }

                if (isset ($product["vat"]) && !empty ($product["vat"])) {
                    $basket_item["vat"] = $product["vat"];
                }

                if (isset ($product["vat_percent"]) && !empty ($product["vat_percent"])) {
                    $basket_item["vat_percent"] = $product["vat_percent"];
                }

                return $basket_item;
            }, $order_info["products"]);

            $order_data['purchase_units']['total_amount'] = $total_amount;
            $order_data['purchase_units']['basket'] = $basket;

            if (isset($order_info["delivery_cost"]) && !empty($order_info["delivery_cost"])) {
                $order_data['purchase_units']['delivery']['amount'] = $order_info["delivery_cost"];
                $order_data['purchase_units']['total_amount'] += $order_info["delivery_cost"];
            }

            if (!empty($this->failure_url) && !empty($this->success_url)) {
                $order_data['redirect_urls'] = [
                    "fail"    => $this->failure_url,
                    "success" => $this->success_url,
                ];
            }

            $json = $this->postOrder($order_data);
            //$json = $order_data;
        }

        return $json;
    }

    /**
     * order_info: https://api.bog.ge/docs/en/payments/standard-process/get-payment-details
     *
     * @param  mixed $order_id
     * @return mixed
     */
    public function order_info(string $order_id): array
    {
        $json = [];

        $client = new Client();

        try {
            $headers = [
                'Authorization'   => 'Bearer ' . $this->access_token,
                'Content-Type'    => 'application/json',
                'Accept-Language' => $this->language,
            ];

            if (!empty($this->theme)) {
                $headers['Theme'] = $this->theme;
            }

            $response = $client->get("https://api.bog.ge/payments/v1/receipt/{$order_id}", [
                'headers' => $headers,
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

        return $json;
    }


    private function postOrder(array $order_info)
    {
        $json = [];

        $client = new Client();

        try {
            $headers = [
                'Authorization'   => 'Bearer ' . $this->access_token,
                'Content-Type'    => 'application/json',
                'Accept-Language' => $this->language,
            ];

            if (!empty($this->theme)) {
                $headers['Theme'] = $this->theme;
            }

            $response = $client->post('https://api.bog.ge/payments/v1/ecommerce/orders', [
                'headers' => $headers,
                'json'    => $order_info,
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

        return $json;
    }

}
