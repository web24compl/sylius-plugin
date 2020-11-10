<?php

namespace Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy;

use Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\Result\ItnResponseType;

interface ItnStrategyInterface
{
    public function add(ItnResponseType $itn_result): void;

    public function handle(string $transactionStatus, string $paymentStatus): array;
}
