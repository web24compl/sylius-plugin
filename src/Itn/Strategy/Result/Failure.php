<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Itn\Strategy\Result;

use Bluemedia\SyliusBluepaymentPlugin\Itn\Enum\ItnEnum;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Payment\PaymentTransitions;

final class Failure implements ItnResponseType
{
    public function canProcess(string $transactionStatus, string $paymentStatus): bool
    {
        return $transactionStatus === ItnEnum::PAYMENT_STATUS_FAILURE &&
            in_array($paymentStatus, [
                ItnEnum::PAYMENT_STATE_FAIL,
                ItnEnum::PAYMENT_STATE_PROCESSING,
            ]);
    }

    public function process(): array
    {
        return [
            'order_state' => OrderPaymentTransitions::TRANSITION_REQUEST_PAYMENT,
            'payment_state' => PaymentTransitions::TRANSITION_FAIL,
        ];
    }
}
