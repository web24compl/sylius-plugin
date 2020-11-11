<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Action;

use BlueMedia\Client;
use Payum\Core\Request\Capture;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\UnsupportedApiException;
use Sylius\Component\Core\Model\PaymentInterface;
use Bluemedia\SyliusBluepaymentPlugin\ValueObject\ClientConfiguration;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Bluemedia\SyliusBluepaymentPlugin\ValueObject\ClientConfigurationInterface;
use Bluemedia\SyliusBluepaymentPlugin\Transaction\Redirect\TransactionRedirect;
use Bluemedia\SyliusBluepaymentPlugin\Transaction\Builder\TransactionStandardBuilder;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;

final class CaptureAction implements ActionInterface, ApiAwareInterface
{
    /**
     * @var ClientConfiguration
     */
    private $api;

    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @var CurrencyConverterInterface
     */
    private $currencyConverter;

    public function __construct(
        CurrencyContextInterface $currencyContext,
        CurrencyConverterInterface $currencyConverter
    ) {
        $this->currencyContext = $currencyContext;
        $this->currencyConverter = $currencyConverter;
    }

    public function execute($request): void
    {
        /** @var PaymentInterface $payment */
        $payment = $request->getModel();

        $client = new Client(
            $this->api->getServiceId($this->currencyContext->getCurrencyCode()),
            $this->api->getSharedKey($this->currencyContext->getCurrencyCode())
        );

        $oldAmount = $payment->getAmount();
        $oldCurrency = $payment->getCurrencyCode();

        if ($oldCurrency !== $this->currencyContext->getCurrencyCode()) {
            // Use real cart currency instead of channel base currency
            $realAmount = $this->currencyConverter->convert(
                $oldAmount,
                $oldCurrency,
                $this->currencyContext->getCurrencyCode()
            );

            $payment->setAmount($realAmount);
            $payment->setCurrencyCode($this->currencyContext->getCurrencyCode());
        }

        $result = $client->getTransactionRedirect(TransactionStandardBuilder::build($this->api, $payment));

        $payment->setAmount($oldAmount);
        $payment->setCurrencyCode($oldCurrency);
        $payment->setState(PaymentInterface::STATE_PROCESSING);

        throw new TransactionRedirect($result->getData());
    }

    public function supports($request): bool
    {
        return $request instanceof Capture && $request->getModel() instanceof SyliusPaymentInterface;
    }

    public function setApi($api): void
    {
        if (!$api instanceof ClientConfigurationInterface) {
            throw new UnsupportedApiException(
                'Not supported. Expected an instance of ' . ClientConfigurationInterface::class
            );
        }

        $this->api = $api;
    }
}
