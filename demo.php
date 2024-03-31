<?php
require './vendor/autoload.php';

use Invays\BogPayments\BogPayments;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

class Demo
{

    public function run()
    {
        $bog = new BogPayments(0000, '000000');
        $bog->order->setLanguage('en');
        $bog->order->setCurrency('GEL');
        $bog->order->setCallbackUrl('https://example.com/notify');
        $bog->order->setFailureUrl('https://example.com/failure');
        $bog->order->setSuccessUrl('https://example.com/success');
        $bog->order->setHeaderTheme('dark');

        $order_info = $bog->order->create_order([
            'shop_order_id' => 'OR-855',
            'customer'      => [
                'full_name'    => 'John Doe',
                'masked_email' => 'jonh.doe@example.com',
                'masked_phone' => '0123456789',
            ],
            'products'      => [
                [
                    'product_id' => 'product123',
                    'quantity'   => 3,
                    'price'      => 101.0000,
                ],
                [
                    'product_id' => 'product123de',
                    'quantity'   => 1,
                    'price'      => 500.0000,
                ],

            ],
            'delivery_cost' => 5.0000
        ]);

        //print_r($order_info);

        if (isset($order_info['_links']['redirect']['href'])) {
            echo $order_info['_links']['redirect']['href'];
            echo '<br>';
        }

        if (isset($order_info['id'])) {
            echo 'Order ID: ' . $order_info['id'];
            echo '<br>';
            print_r($bog->order->order_info($order_info['id']));
            echo '<br>';
        }

        $bog = new BogPayments(19454, '', 'installment');
        $bog->installment->setButtonText('My custom text');
        $bog->installment->setCallbackUrl('https://example.com/notify');
        print_r($bog->installment->calculator(2348.44));
    }
}

$test = new Demo();
$test->run();