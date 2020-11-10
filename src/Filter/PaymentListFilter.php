<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Filter;

use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Bluemedia\SyliusBluepaymentPlugin\Enum\BluepaymentEnum;
use Payum\Core\Storage\StorageInterface;

final class PaymentListFilter implements PaymentListFilterInterface
{
    /** @var StorageInterface */
    private $gatewayConfigStore;

    /** @var CurrencyContextInterface */
    private $currencyContext;

    public function __construct(
        CurrencyContextInterface $currencyContext,
        StorageInterface $gatewayConfigStore
    ) {
        $this->currencyContext = $currencyContext;
        $this->gatewayConfigStore = $gatewayConfigStore;
    }

    public function filter(array $methods): array
    {
        $currency = $this->currencyContext->getCurrencyCode();
        foreach ($methods as $key => $method) {
            $gatewayConfig = $this->gatewayConfigStore->findBy(['gatewayName' => $method->getCode()]);

            if ($this->isBluepaymentPlugin($gatewayConfig)) {
                $config = $gatewayConfig[0]->getConfig();
                $service_id = $config['service_id_' . $currency];
                $shared_key = $config['shared_key_' . $currency];

                if (empty($service_id) || empty($shared_key)) {
                    unset($methods[$key]);
                }
            }
        }

        return $methods;
    }

    private function isBluepaymentPlugin($gatewayConfig): bool
    {
        return empty($gatewayConfig) === false &&
            isset($gatewayConfig[0]) &&
            $gatewayConfig[0] instanceof GatewayConfigInterface &&
            $gatewayConfig[0]->getFactoryName() === BluepaymentEnum::PLUGIN_FACTORY_NAME;
    }
}
