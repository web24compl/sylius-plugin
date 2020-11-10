<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\Result;

interface ItnResponseType
{
    public function canProcess(string $transactionStatus, string $paymentStatus): bool;

    public function process(): array;
}
