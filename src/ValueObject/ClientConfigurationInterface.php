<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\ValueObject;

interface ClientConfigurationInterface
{
    public function getGatewayUrl(): string;

    public function getServiceId(string $currency): string;

    public function getSharedKey(string $currency): string;
}
