<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Exceptions\PaymentException;

class GatewayManager
{
    private array $gateways = [];

    public function __construct()
    {
        $this->registerGatewaysFromConfig();
    }

    private function registerGatewaysFromConfig(): void
    {
        $gateways = config('payment.gateways', []);

        foreach ($gateways as $name => $class) {
            $this->gateways[$name] = app($class);
        }
    }


    public function gateway(string $method): PaymentGatewayInterface
    {
        if (!isset($this->gateways[$method])) {
            throw PaymentException::invalidGateway($method);
        }

        $gateway = $this->gateways[$method];

        if (!$gateway->isConfigured()) {
            throw PaymentException::gatewayNotConfigured($method);
        }

        return $gateway;
    }

    public function availableGateways(): array
    {
        return array_keys($this->gateways);
    }

    public function hasGateway(string $method): bool
    {
        return isset($this->gateways[$method]);
    }
}
