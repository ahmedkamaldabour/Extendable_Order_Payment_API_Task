<?php

return [

    'gateways' => [
        'credit_card' => \App\Services\Payment\Gateways\CreditCardGateway::class,
        'paypal' => \App\Services\Payment\Gateways\PayPalGateway::class,
        'stripe' => \App\Services\Payment\Gateways\StripeGateway::class,
    ],

];
