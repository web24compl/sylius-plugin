<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Util\Formatter;

final class PriceFormatter
{
    public static function toDecimals(int $amount): string
    {
        return number_format($amount / 100, 2, '.', '');
    }
}
