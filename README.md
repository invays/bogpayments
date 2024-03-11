## Introduction

This library was created to simplify software development for various PHP frameworks and CMS. It allows you to easily integrate your website or service with the Bank of Georgia payment system.

The library provides the basic functions for creating a payment form, reading order information, and returning an order to the user's personal account manager.

## Business Notes:

The library is designed to allow you to create multiple online stores and use it with different accounts.
Therefore, this library can be used in e-commerce with a franchise model.

## Dev Notes

The library use Guzzle http client ("guzzlehttp/guzzle": "^7.4"). So be sure that you have updated it.

## Installation

Install the package via Composer. Run the Composer require command from the Terminal:

```
composer require invays/bog-payments
```

## Example

Use included variables for template.

```php
use Invays\BogPayments\BogPayments;

$bog->order->setLanguage('en');
$bog->order->setCurrency('GEL');
$bog->order->setCallbackUrl('https://example.com/notify');

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
```

Use custom user variable. with theme and redirect links.

```php
use Invays\BogPayments\BogPayments;

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
```

Creare refund

```php
use Invays\BogPayments\BogPayments;

$bog = new BogPayments(0000, '000000');
$bog->refund->create_refund('order_id', 10.0);

```
