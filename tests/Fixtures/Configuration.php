<?php
declare(strict_types=1);

namespace Tests\Bluemedia\SyliusBluepaymentPlugin\Fixtures;

abstract class Configuration
{
    public const GATEWAY_URL = 'https://pay-accept.bm.pl';

    public static function getClientConfiguration(): array
    {
        return [
            'service_id_PLN' => '123456',
            'shared_key_PLN' => '79884d46f3c371a21ccba1ee845fc1f26fedd57a',
            'test_mode' => true
        ];
    }
}
