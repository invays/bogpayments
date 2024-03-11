<?php
declare(strict_types=1);
namespace Invays\BogPayments\Tests;

use Invays\BogPayments\BogPayments;
use PHPUnit\Framework\TestCase;

class BogPaymentsTest extends TestCase
{
    const CLIENT_ID = '123456';
    const CLIENT_SECRET = '123456';

    public function testCreatePaymentLink(): void
    {
        $bog = new BogPayments(self::CLIENT_ID, self::CLIENT_SECRET);
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
                    'quantity'   => 1,
                    'price'      => 100,
                ],
                [
                    'product_id' => 'product123de',
                    'quantity'   => 1,
                    'price'      => 525,
                ],

            ],
            'delivery_cost' => 1000
        ]);

        $this->assertIsString($order_info['_links']['redirect']['href']);
    }

    public function testCreatePaymentOrderId(): void
    {
        $bog = new BogPayments(self::CLIENT_ID, self::CLIENT_SECRET);
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
                    'quantity'   => 1,
                    'price'      => 100,
                ],
                [
                    'product_id' => 'product123de',
                    'quantity'   => 1,
                    'price'      => 525,
                ],

            ],
            'delivery_cost' => 1000
        ]);

        $this->assertIsString($order_info['id']);
    }


}