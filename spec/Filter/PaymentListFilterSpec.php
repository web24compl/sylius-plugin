<?php
declare(strict_types=1);

namespace spec\Bluemedia\SyliusBluepaymentPlugin\Filter;

use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use PhpSpec\ObjectBehavior;
use Payum\Core\Storage\StorageInterface;
use Bluemedia\SyliusBluepaymentPlugin\Filter\PaymentListFilter;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Bluemedia\SyliusBluepaymentPlugin\Filter\PaymentListFilterInterface;
use Tests\Bluemedia\SyliusBluepaymentPlugin\Fixtures\Configuration;

class PaymentListFilterSpec extends ObjectBehavior
{
    public function let(
        CurrencyContextInterface $currencyContext,
        StorageInterface $gatewayConfigStore
    ): void {
        $currencyContext->getCurrencyCode()->willReturn('PLN');

        $this->beConstructedWith($currencyContext, $gatewayConfigStore);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(PaymentListFilter::class);
    }

    public function it_implements_action_interface(): void
    {
        $this->shouldHaveType(PaymentListFilterInterface::class);
    }

    public function it_filter_bluepayment_payment_methods(
        PaymentMethodInterface $bmPaymentMehod,
        PaymentMethodInterface $otherPaymentMethod,
        GatewayConfigInterface $bmConfigInterface,
        GatewayConfigInterface $otherConfigInterface,
        $gatewayConfigStore
    ): void {
        $bmPaymentMehod->getCode()->willReturn('bluepayment');
        $otherPaymentMethod->getCode()->willReturn('bank_transfer');

        $methods = [
            $bmPaymentMehod,
            $otherPaymentMethod
        ];

        $bmConfigInterface->getConfig()->willReturn(Configuration::getClientConfiguration());
        $bmConfigInterface->getFactoryName()->willReturn('bluepayment');

        $gatewayConfigStore->findBy(['gatewayName' => 'bluepayment'])->willReturn([$bmConfigInterface]);
        $gatewayConfigStore->findBy(['gatewayName' => 'bank_transfer'])->willReturn([$otherConfigInterface]);
        $this->filter($methods);
    }

    public function it_unsets_bluepayment_without_credentials(
        PaymentMethodInterface $bmPaymentMehod,
        GatewayConfigInterface $bmConfigInterface,
        $gatewayConfigStore
    ): void {
        $bmPaymentMehod->getCode()->willReturn('bluepayment');

        $bmConfigInterface->getConfig()->willReturn([
            'service_id_PLN' => '',
            'shared_key_PLN' => ''
        ]);

        $bmConfigInterface->getFactoryName()->willReturn('bluepayment');
        $gatewayConfigStore->findBy(['gatewayName' => 'bluepayment'])->willReturn([$bmConfigInterface]);

        $this->filter([$bmPaymentMehod]);
    }
}
