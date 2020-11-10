<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy;

use Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\Result\ItnResponseType;

final class Itn implements ItnStrategyInterface
{
    private $itn_result = [];

    public function add(ItnResponseType $itn_result): void
    {
        $this->itn_result[] = $itn_result;
    }

    public function handle(string $transactionStatus, string $paymentStatus): array
    {
        foreach ($this->itn_result as $itn) {
            if ($itn->canProcess($transactionStatus, $paymentStatus)) {
                return $itn->process();
            }
        }

        return [];
    }
}
