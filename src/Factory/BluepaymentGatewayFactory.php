<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Factory;

use Bluemedia\SyliusBluepaymentPlugin\Builder\ClientConfigurationBuilder;
use Bluemedia\SyliusBluepaymentPlugin\Enum\BluepaymentEnum;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

final class BluepaymentGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
    */
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => BluepaymentEnum::PLUGIN_FACTORY_NAME,
            'payum.factory_title' => 'bluemedia_sylius_bluepayment_plugin.custom_name',
        ]);

        $config['payum.api'] = ClientConfigurationBuilder::build($config);
    }
}
