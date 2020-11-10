<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Transaction\Builder;

use Sylius\Component\Core\Model\PaymentInterface;
use Bluemedia\SyliusBluepaymentPlugin\Util\ParamSuffixer;
use Bluemedia\SyliusBluepaymentPlugin\Enum\BluepaymentEnum;
use Bluemedia\SyliusBluepaymentPlugin\Util\Formatter\PriceFormatter;
use Bluemedia\SyliusBluepaymentPlugin\ValueObject\ClientConfigurationInterface;

final class TransactionStandardBuilder
{
    public static function build(ClientConfigurationInterface $configuration, PaymentInterface $payment): array
    {
        return [
            'gatewayUrl' => $configuration->getGatewayUrl(),
            'transaction' => [
                'orderID' => ParamSuffixer::addSuffix((string) $payment->getOrder()->getId()),
                'amount' => PriceFormatter::toDecimals($payment->getAmount()),
                'description' => sprintf(
                    '%s %s',
                    BluepaymentEnum::TRANSACTION_DESCRIPTION_SUFFIX,
                    $payment->getOrder()->getId()
                ),
                'currency' => $payment->getCurrencyCode(),
                'customerEmail' => $payment->getOrder()->getCustomer()->getEmail()
            ]
        ];
    }
}
