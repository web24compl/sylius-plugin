<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\ValueObject;

final class ClientConfiguration implements ClientConfigurationInterface
{
    private $gatewayUrl;
    private $serviceIds;
    private $sharedKeys;

    public function __construct(
        array $serviceIds,
        array $sharedKeys,
        string $gatewayUrl
    ) {
        $this->serviceIds = $serviceIds;
        $this->sharedKeys = $sharedKeys;
        $this->gatewayUrl = $gatewayUrl;
    }

    public function getGatewayUrl(): string
    {
        return $this->gatewayUrl;
    }

    public function getServiceId(string $currency): string
    {
        return (string) $this->serviceIds['service_id_' . $currency];
    }

    public function getSharedKey(string $currency): string
    {
        return $this->sharedKeys['shared_key_' . $currency];
    }
}
