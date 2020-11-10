<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Enum;

abstract class BluepaymentEnum
{
    public const PLUGIN_FACTORY_NAME = 'bluepayment';

    public const LIVE_DOMAIN = 'https://pay.bm.pl';
    public const SANDBOX_DOMAIN = 'https://pay-accept.bm.pl';

    public const TRANSACTION_DESCRIPTION_SUFFIX = 'Sylius BM payment ID';
}
