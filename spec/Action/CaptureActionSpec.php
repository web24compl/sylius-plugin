<?php
declare(strict_types=1);

namespace spec\Bluemedia\SyliusBluepaymentPlugin\Action;

use PhpSpec\ObjectBehavior;
use Payum\Core\Request\Capture;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Action\ActionInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Payum\Core\Exception\UnsupportedApiException;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Bluemedia\SyliusBluepaymentPlugin\Action\CaptureAction;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Bluemedia\SyliusBluepaymentPlugin\Transaction\Redirect\TransactionRedirect;
use Bluemedia\SyliusBluepaymentPlugin\ValueObject\ClientConfigurationInterface;

class CaptureActionSpec extends ObjectBehavior
{
    public function let(
        CurrencyContextInterface $currencyContext,
        CurrencyConverterInterface $currencyConverter
    ): void {
        $currencyConverter->convert(2000, 'PLN', 'EUR')->willReturn(8000);
        $this->beConstructedWith($currencyContext, $currencyConverter);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CaptureAction::class);
    }

    public function it_implements_action_interface(): void
    {
        $this->shouldHaveType(ActionInterface::class);
    }

    public function it_implements_api_aware_interface(): void
    {
        $this->shouldHaveType(ApiAwareInterface::class);
    }

    public function it_executes_eur_and_throw_redirect(
        Capture $request,
        PaymentInterface $payment,
        OrderInterface $order,
        CustomerInterface $customer,
        ClientConfigurationInterface $clientConfiguration,
        $currencyContext
    ): void {
        $clientConfiguration->getServiceId('EUR')->willReturn('123456');
        $clientConfiguration->getSharedKey('EUR')->willReturn('testkey');
        $clientConfiguration->getGatewayUrl()->willReturn('http://localhost');
        $currencyContext->getCurrencyCode()->willReturn('EUR');

        $this->setApi($clientConfiguration);

        $customer->getEmail()->willReturn('test@test.test');
        $order->getId()->willReturn(123);
        $order->getCustomer()->willReturn($customer);

        $payment->getAmount()->willReturn(2000);
        $payment->getCurrencyCode()->willReturn('PLN');
        $payment->getOrder()->willReturn($order);

        $request->getModel()->willReturn($payment);

        $payment->setAmount(8000)->shouldBeCalledOnce();
        $payment->setCurrencyCode('EUR')->shouldBeCalledOnce();

        $payment->setAmount(2000)->shouldBeCalledOnce();
        $payment->setCurrencyCode('PLN')->shouldBeCalledOnce();
        $payment->setState('processing')->shouldBeCalledOnce();

        $this->shouldThrow(TransactionRedirect::class)
            ->during('execute', [$request]);
    }

    public function it_executes_pln_and_throw_redirect(
        Capture $request,
        PaymentInterface $payment,
        OrderInterface $order,
        CustomerInterface $customer,
        ClientConfigurationInterface $clientConfiguration,
        $currencyContext
    ): void {
        $clientConfiguration->getServiceId('PLN')->willReturn('123456');
        $clientConfiguration->getSharedKey('PLN')->willReturn('testkey');
        $clientConfiguration->getGatewayUrl()->willReturn('http://localhost');
        $currencyContext->getCurrencyCode()->willReturn('PLN');

        $this->setApi($clientConfiguration);

        $customer->getEmail()->willReturn('test@test.test');
        $order->getId()->willReturn(123);
        $order->getCustomer()->willReturn($customer);

        $payment->getAmount()->willReturn(2000);
        $payment->getCurrencyCode()->willReturn('PLN');
        $payment->getOrder()->willReturn($order);

        $request->getModel()->willReturn($payment);

        $payment->setAmount(2000)->shouldBeCalledOnce();
        $payment->setCurrencyCode('PLN')->shouldBeCalledOnce();
        $payment->setState('processing')->shouldBeCalledOnce();

        $this->shouldThrow(TransactionRedirect::class)
            ->during('execute', [$request]);
    }

    public function it_supports_only_capture_request(
        Capture $request,
        PaymentInterface $payment
    ): void {
        $request->getModel()->willReturn($payment);

        $this->supports($request)->shouldReturn(true);
    }

    public function it_throws_unsupported_api_exception(): void
    {
        $this->shouldThrow(UnsupportedApiException::class)
            ->during('setApi', ['something']);
    }
}
