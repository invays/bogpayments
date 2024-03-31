<?php
namespace Invays\BogPayments\Order;

class Status
{

    public const STATUSES = [
        'created',
        'processing',
        'completed',
        'rejected',
        'refund_requested',
        'refunded',
        'refunded_partially',
        'auth_requested',
        'blocked',
        'partial_completed',
    ];

}
