<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Filter;

interface PaymentListFilterInterface
{
    public function filter(array $methods): array;
}
