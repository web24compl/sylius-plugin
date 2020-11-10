<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Util;

final class ParamSuffixer
{
    public static function addSuffix(string $param): string
    {
        return sprintf('%s-%s', $param, time());
    }

    public static function removeSuffix(string $param): string
    {
        $param_parts = explode('-', $param);

        return $param_parts[0];
    }
}
