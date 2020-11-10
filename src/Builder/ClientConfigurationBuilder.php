<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Builder;

use Payum\Core\Bridge\Spl\ArrayObject;
use Bluemedia\SyliusBluepaymentPlugin\Enum\BluepaymentEnum;
use Bluemedia\SyliusBluepaymentPlugin\ValueObject\ClientConfiguration;

final class ClientConfigurationBuilder implements ClientConfigurationBuilderInterface
{
    public static function build(ArrayObject $payumConfig): ClientConfiguration
    {
        return new ClientConfiguration(
            self::filterByPattern('/service_id_/', $payumConfig->toUnsafeArray()),
            self::filterByPattern('/shared_key_/', $payumConfig->toUnsafeArray()),
            self::getGatewayDomain($payumConfig->get('test_mode'))
        );
    }

    private static function getGatewayDomain(bool $testMode): string
    {
        return $testMode ? BluepaymentEnum::SANDBOX_DOMAIN : BluepaymentEnum::LIVE_DOMAIN;
    }

    private static function filterByPattern(string $pattern, array $data): array
    {
        $keys = preg_grep($pattern, array_keys($data));
        $values = array_intersect_key($data, array_flip($keys));

        return array_filter($values);
    }
}
